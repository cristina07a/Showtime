<?php

namespace App\Controller;


use App\Entity\Code;
use App\Form\CodeType;
use App\Repository\CodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/code')]
final class CodeController extends AbstractController
{
    #[Route(name: 'app_code_index', methods: ['GET'])]
    public function index(CodeRepository $codeRepository): Response
    {
        return $this->render('code/index.html.twig', [
            'codes' => $codeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_code_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $code = new Code();
        $form = $this->createForm(CodeType::class, $code);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($code);
            $entityManager->flush();

            return $this->redirectToRoute('app_code_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('code/new.html.twig', [
            'code' => $code,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_code_show', methods: ['GET'])]
    public function show(Code $code): Response
    {
        return $this->render('code/show.html.twig', [
            'code' => $code,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_code_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Code $code, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CodeType::class, $code);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_code_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('code/edit.html.twig', [
            'code' => $code,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_code_delete', methods: ['POST'])]
    public function delete(Request $request, Code $code, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $code->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($code);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_code_index', [], Response::HTTP_SEE_OTHER);
    }
}
