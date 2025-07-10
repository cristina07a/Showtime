<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Festival;
use App\Entity\Ticket;
use App\Form\BookingAuthenticated;
use App\Form\BookingUnauthenticated;
use App\Repository\BandRepository;
use App\Repository\CodeRepository;
use App\Repository\FestivalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/admin', name: 'app_home_admin')]
    public function index(): Response
    {
        return $this->render('home_admin/index.html.twig');
    }

    /*#[Route('/admin', name: 'app_home_admin', methods: ['GET'])]
    public function index(BookingRepository $bookingRepository): Response
    {
        return $this->render('home_admin/index.html.twig', [
            'bookings' => $bookingRepository->getOverallSales(),
        ]);
    }*/

    #[Route('/user', name: 'app_home_user', methods: ['GET'])]
    public function index_user(FestivalRepository $festivalRepository): Response
    {
        return $this->render('home_user/index.html.twig', [
            'festivals' => $festivalRepository->findAll(),
        ]);
    }

    #[Route('/list/bands', name: 'app_band_show_for_user', methods: ['GET'])]
    public function showBandForUser(BandRepository $bandRepository): Response
    {
        return $this->render('home_user/band_list.html.twig', [
            'bands' => $bandRepository->findAll(),
        ]);
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

                    if ($code) {
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
                        $this->addFlash('error', 'Invalid or inapplicable discount code.');
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
