<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/villes', name: 'villes_')]
class VilleController extends AbstractController
{
    #[Route('/', name: 'liste')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $villes = $entityManager->getRepository(Ville::class)->findAll();

        return $this->render('ville/liste.html.twig', [
            'villes' => $villes,
        ]);
    }


    #[Route('/ajouter', name: 'ajouter')]
    public function ajouter(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ville = new Ville();
        $form = $this->createForm(VilleFormType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ville);
            $entityManager->flush();

            $this->addFlash('success', 'Ville ajoutée avec succès !');

            return $this->redirectToRoute('villes_liste');
        }

        return $this->render('ville/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
