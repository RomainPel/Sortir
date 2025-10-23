<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/lieux', name: 'lieux_')]
class LieuController extends AbstractController
{
    #[Route('/supprimer/{id}', name: 'supprimer', requirements: ['id' => '\d+'])]
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        $lieu = $entityManager->getRepository(Lieu::class)->find($id);

        if (!$lieu) {
            throw $this->createNotFoundException('Lieu non trouvé');
        }

        $entityManager->remove($lieu);
        $entityManager->flush();

        $this->addFlash('success', 'Lieu supprimé avec succès !');

        return $this->redirectToRoute('lieux_liste');
    }

    #[Route('/modifier/{id}', name: 'modifier', requirements: ['id' => '\d+'])]
    public function modifier(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $lieu = $entityManager->getRepository(Lieu::class)->find($id);

        if (!$lieu) {
            throw $this->createNotFoundException('Lieu non trouvé');
        }

        $form = $this->createForm(LieuFormType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Lieu modifié avec succès !');
            return $this->redirectToRoute('lieux_liste');
        }

        return $this->render('lieu/modifier.html.twig', [
            'form' => $form->createView(),
            'lieu' => $lieu,
        ]);
    }

    #[Route('/', name: 'liste')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $lieux = $entityManager->getRepository(Lieu::class)->findAll();

        return $this->render('lieu/liste.html.twig', [
            'lieux' => $lieux,
        ]);
    }

    #[Route('/ajouter', name: 'ajouter')]
    public function ajouter(Request $request, EntityManagerInterface $entityManager): Response
    {
        $lieu = new Lieu();
        $form = $this->createForm(LieuFormType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($lieu);
            $entityManager->flush();

            $this->addFlash('success', 'Lieu ajouté avec succès !');

            return $this->redirectToRoute('lieux_liste');
        }

        return $this->render('lieu/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
