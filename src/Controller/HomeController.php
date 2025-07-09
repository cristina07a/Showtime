<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Festival;
use App\Entity\Ticket;
use App\Form\BookingCreateType;
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

    #[Route('/user', name: 'app_home_user', methods: ['GET'])]
    public function index_user(FestivalRepository $festivalRepository): Response
    {
        return $this->render('home_user/index.html.twig', [
            'festivals' => $festivalRepository->findAll(),
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
    public function bookTicket(Ticket $ticket, Request $request, EntityManagerInterface $entityManager): Response
    {
        // festival/2/bookings
        $booking = new Booking();
        $booking->setTicket($ticket);

        $user = $this->getUser();
        if ($user) {
            $booking->setUser($user);
        }

        $form = $this->createForm(BookingCreateType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($booking);
            $entityManager->flush();

            return $this->redirectToRoute('app_home_user', [], Response::HTTP_SEE_OTHER);
        }

        $pathOfBooking = $user
            ? 'booking/_create_authenticated.html.twig'
            : 'booking/_create_unauthenticated.html.twig';

        return $this->render('booking/create.html.twig', [
            'booking' => $booking,
            'form' => $form,
            'ticket' => $ticket,
            'pathOfBooking' => $pathOfBooking,
        ]);
    }
}
