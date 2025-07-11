<?php

namespace App\Controller;

use App\Entity\Band;
use App\Entity\Booking;
use App\Entity\Festival;
use App\Entity\Ticket;
use App\Form\BookingAuthenticated;
use App\Form\BookingUnauthenticated;
use App\Repository\BandRepository;
use App\Repository\BookingRepository;
use App\Repository\CodeRepository;
use App\Repository\FestivalRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class HomeController extends AbstractController
{
    #[Route('/admin', name: 'app_home_admin')]
    public function index(BookingRepository $bookingRepository): Response
    {
        $bookings = $bookingRepository->findAll();

        $numberOfSoldTickets = count($bookings);
        $totalRevenue = 0;

        foreach ($bookings as $booking) {
            $totalRevenue += $booking->getPaidAmount();
        }

        return $this->render('home_admin/index.html.twig', [
            'numberOfSoldTickets' => $numberOfSoldTickets,
            'totalRevenue' => $totalRevenue,
        ]);
    }

    #[Route('/user', name: 'app_home_user', methods: ['GET'])]
    public function index_user(Request $request, FestivalRepository $festivalRepository): Response
    {
        $search = $request->query->get('search', '');
        $user = $this->getUser();
        $userBands = [];

        if ($user) {
            $userBands = $user->getBands();
        }

        if ($search) {
            $festivals = $festivalRepository->findByName($search);
            $recommendedFestivals = [];
        } else {
            $allFestivals = $festivalRepository->findAll();

            $recommendedFestivals = [];

            foreach ($allFestivals as $festival) {
                $matchingBandsCount = 0;
                foreach ($festival->getBands() as $band) {
                    if ($userBands && $userBands->contains($band)) {
                        $matchingBandsCount++;
                    }
                }
                if ($matchingBandsCount >= 3) {
                    $recommendedFestivals[] = $festival;
                }
            }

            $festivals = $allFestivals;
        }

        return $this->render('home_user/index.html.twig', [
            'festivals' => $festivals,
            'recommendedFestivals' => $recommendedFestivals,
            'search' => $search,
        ]);
    }


    #[Route('/list/bands', name: 'app_band_show_for_user', methods: ['GET'])]
    public function showBandForUser(Request $request, BandRepository $bandRepository): Response
    {
        $search = $request->query->get('search', '');

        if ($search) {
            $bands = $bandRepository->findByName($search);
        } else {
            $bands = $bandRepository->findAll();
        }

        $recommendations = [];
        $recommendationsFile = $this->getParameter('kernel.project_dir') . '/tools/artist_recommendations.json';

        if (file_exists($recommendationsFile)) {
            $jsonContent = file_get_contents($recommendationsFile);
            $recommendations = json_decode($jsonContent, true);
        }

        $recommendedBands = [];
        $user = $this->getUser();

        if ($user) {
            $userWishlistBands = $user->getBands();

            //Band to string for searching in JSON
            $wishlistBandNames = array_map(fn($band) => $band->getName(), $userWishlistBands->toArray());

            $recommendedBandNames = [];


            // $bandName = '50 Cent'
            // $recommendedBandNames = ["Dr. Dre", "Snoop Dogg", "T.I.", "Eminem", "JAY Z"];

            foreach ($wishlistBandNames as $bandName) {
                $recommendedBandNames = array_merge($recommendedBandNames,
                    $recommendations[$bandName]);
            }

            //no duplicates
            $recommendedBandNames = array_unique($recommendedBandNames);

            //removes what's in wishlist
            $filteredNames = array_filter($recommendedBandNames, fn($name) => !in_array($name, $wishlistBandNames));

            $limitedNames = array_slice($filteredNames, 0, 10);

            foreach ($limitedNames as $bandName) {
                $foundBands = $bandRepository->findByName($bandName);
                $recommendedBands = array_merge($recommendedBands, $foundBands);
            }
        }

        return $this->render('home_user/band_list.html.twig', [
            'bands' => $bands,
            'search' => $search,
            'recommendations' => $recommendations,
            'recommendedBands' => $recommendedBands,
        ]);
    }


    #[Route('/wishlist/user/{id}', name: 'app_user_wishlist', methods: ['GET'])]
    public function userWishlist(UserRepository $userRepository, $id, EntityManagerInterface $em): Response
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $bands = $user->getBands();

        return $this->render('home_user/user_wishlist.html.twig', [
            'user' => $user,
            'bands' => $bands,
        ]);
    }

    #[Route('/wishlist/toggle/{id}', name: 'app_toggle_wishlist', methods: ['POST'])]
    public function toggleWishlist(Band $band, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();

        if ($user->getBands()->contains($band)) {
            $user->removeBand($band);
            $inWishlist = false;
        } else {
            $user->addBand($band);
            $inWishlist = true;
        }

        $em->persist($user);
        $em->flush();

        return new JsonResponse(['inWishlist' => $inWishlist]);
    }

    #[Route('/ticket/festival/{id}', name: 'app_ticket_show_for_festival', methods: ['GET'])]
    public function showTicketsForFestival(Festival $festival): Response
    {
        $tickets = $festival->getTickets();

        return $this->render('home_user/ticket_list.html.twig', [
            'festival' => $festival,
            'tickets' => $tickets,
        ]);
    }

    #[Route('/booking/create/{id}', name: 'app_booking_create', methods: ['GET', 'POST'])]
    public function bookTicket(Ticket $ticket, Request $request, EntityManagerInterface $entityManager, CodeRepository $codeRepository): Response
    {
        $booking = new Booking();
        $booking->setTicket($ticket);

        $user = $this->getUser();
        if ($user) {
            $booking->setUser($user);
            $booking->setBookingEmail($user->getEmail());
        }

        $originalPrice = $ticket->getPrice();
        $finalPrice = $originalPrice;

        $session = $request->getSession();
        $appliedCode = $session->get('applied_code_' . $ticket->getId());

        if ($appliedCode) {
            $code = $codeRepository->findOneByNameAndFestival($appliedCode, $ticket->getFestival());
            if ($code) {
                $discountPercentage = $code->getPercentage();
                $discountAmount = ($originalPrice * $discountPercentage) / 100;
                $finalPrice = $originalPrice - $discountAmount;
            }
        }

        $booking->setPaidAmount($finalPrice);
        if ($user) {
            $form = $this->createForm(BookingAuthenticated::class, $booking);
            $pathOfBooking = 'booking/_create_authenticated.html.twig';
        } else {
            $form = $this->createForm(BookingUnauthenticated::class, $booking);
            $pathOfBooking = 'booking/_create_unauthenticated.html.twig';
        }
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $discountCode = $form->get('code')->getData();

            if ($request->request->has('apply_code')) {

                if ($discountCode) {
                    $festival = $ticket->getFestival();
                    $code = $codeRepository->findOneByNameAndFestival($discountCode, $festival);

                    if ($code && $code->isAvailable()) {
                        $discountPercentage = $code->getPercentage();
                        $discountAmount = ($originalPrice * $discountPercentage) / 100;
                        $finalPrice = $originalPrice - $discountAmount;

                        $session->set('applied_code_' . $ticket->getId(), $discountCode);

                        $this->addFlash('success', sprintf(
                            'Discount code "%s" applied! You saved %.2f RON (%.0f%% discount)',
                            $discountCode,
                            $discountAmount,
                            $discountPercentage
                        ));
                    } else {
                        $session->remove('applied_code_' . $ticket->getId());
                        $this->addFlash('error', 'Invalid or unavailable discount code.');
                    }
                } else {
                    $session->remove('applied_code_' . $ticket->getId());
                    $finalPrice = $originalPrice;
                }

                return $this->redirectToRoute('app_booking_create', ['id' => $ticket->getId()]);

            } elseif ($form->isValid()) {

                $booking->setPaidAmount($finalPrice);

                $entityManager->persist($booking);
                $entityManager->flush();

                $session->remove('applied_code_' . $ticket->getId());

                return $this->redirectToRoute('app_home_user', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('booking/create.html.twig', [
            'booking' => $booking,
            'form' => $form,
            'ticket' => $ticket,
            'pathOfBooking' => $pathOfBooking,
            'originalPrice' => $originalPrice,
            'finalPrice' => $finalPrice,
        ]);
    }
}
