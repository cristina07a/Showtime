<?php

namespace App\Controller;

use App\Repository\FestivalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/admin', name: 'app_home_admin')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    #[Route('/user', name: 'app_home_user', methods: ['GET'])]
    public function index_user(FestivalRepository $festivalRepository): Response
    {
        return $this->render('home_user/index.html.twig', [
            'festivals' => $festivalRepository->findAll(),
        ]);
    }
}
