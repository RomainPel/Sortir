<?php

namespace App\Controller;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\SortiesFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sorties', name: 'sorties_')]
final class SortiesController extends AbstractController
{

    #[Route('/{id}/desinscription', name: 'desinscription')]
    public function desinscription(Sortie $sortie, EntityManagerInterface $em, Security $security): Response
    {
        $participant = $security->getUser();

        if (!$participant) {
            $this->addFlash('error', 'Vous devez être connecté pour vous désinscrire.');
            return $this->redirectToRoute('app_login');
        }

        // Vérifie si le participant est inscrit
        if (!$sortie->getParticipants()->contains($participant)) {
            $this->addFlash('warning', 'Vous n’êtes pas inscrit à cette sortie.');
        } else {
            // Retire le participant depuis la sortie
            $sortie->removeParticipant($participant);
            $em->persist($sortie);
            $em->flush();

            $this->addFlash('success', 'Vous avez été désinscrit avec succès ❌');
        }

        return $this->redirectToRoute('sorties_details', ['id' => $sortie->getId()]);
    }





    #[Route('/{id}/inscription', name: 'inscription')]
    public function inscription(Sortie $sortie, EntityManagerInterface $em, Security $security): Response
    {
        $participant = $security->getUser();

        if (!$participant) {
            $this->addFlash('error', 'Vous devez être connecté pour vous inscrire.');
            return $this->redirectToRoute('app_login');
        }

        // Vérifie si déjà inscrit
        if ($sortie->getParticipants()->contains($participant)) {
            $this->addFlash('warning', 'Vous êtes déjà inscrit à cette sortie.');
        } else {
            // Vérifie s’il reste de la place
            if ($sortie->getParticipants()->count() >= $sortie->getNbinscriptionmax()) {
                $this->addFlash('danger', 'La sortie est complète.');
            } else {
                // Ajoute le participant à la sortie (côté Sortie)
                $sortie->addParticipant($participant);
                $em->persist($sortie);
                $em->flush();

                $this->addFlash('success', 'Inscription réussie ✅');
            }
        }

        return $this->redirectToRoute('sorties_details', ['id' => $sortie->getId()]);
    }

    #[Route('/modifier/{id}', name: 'modifier', requirements: ['id' => '\d+'])]
    public function modifier(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $sortie = $entityManager->getRepository(Sortie::class)->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException('Sortie non trouvée.');
        }

        $form = $this->createForm(SortiesFormType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Sortie modifiée avec succès !');

            return $this->redirectToRoute('sorties_details', ['id' => $sortie->getId()]);
        }

        return $this->render('sorties/modifier.html.twig', [
            'form' => $form->createView(),
            'sortie' => $sortie,
        ]);
    }

    #[Route('/{id}', name: 'details', requirements: ['id' => '\d+'])]
    public function detail(int $id, EntityManagerInterface $em): Response
    {
        $sortie = $em->getRepository(Sortie::class)->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException('Sortie non trouvée');
        }

        return $this->render('sorties/details.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    #[Route('/', name: 'liste')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $sorties = $entityManager->getRepository(Sortie::class)->findAll();

        return $this->render('sorties/liste.html.twig', [
            'sorties' => $sorties,
        ]);
    }

    #[Route('/ajouter', name: 'ajouter')]
    public function ajouter(Request $request, EntityManagerInterface $entityManager): Response
    {



        $sortie = new Sortie();
        $form = $this->createForm(SortiesFormType::class, $sortie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortie->setOrganisateur($this->getUser());

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
