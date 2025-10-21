<?php

namespace App\Controller;

use App\Entity\Sorties;
use App\Form\SortiesFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortiesController extends AbstractController
{

    #[Route('/sorties/{id}', name: 'sorties_details')]
    public function detail(int $id, EntityManagerInterface $em): Response
    {
        $sortie = $em->getRepository(Sorties::class)->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException('Sortie non trouvée');
        }

        return $this->render('sorties/details.html.twig', [
            'sortie' => $sortie,
        ]);
    }


    #[Route('/sorties', name: 'sorties_liste')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $sorties = $entityManager->getRepository(Sorties::class)->findAll();

        return $this->render('sorties/liste.html.twig', [
            'sorties' => $sorties,
        ]);
    }


    #[Route('/sorties/ajouter', name: 'sorties_ajouter')]
    public function ajouter(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sortie = new Sorties();
        $form = $this->createForm(SortiesFormType::class, $sortie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'Sortie ajoutée avec succès !');

            return $this->redirectToRoute('sorties_ajouter');
        }

        return $this->render('sorties/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

