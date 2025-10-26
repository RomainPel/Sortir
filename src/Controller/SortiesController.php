<?php

namespace App\Controller;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Etat;
use App\Form\SortiesFormType;
use App\Repository\SortiesRepository;
use App\Service\FileUploaderProfil;
use App\Service\FileUploaderSortie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sorties', name: 'sorties_')]
final class SortiesController extends AbstractController
{

    #[Route('/{id}/cloturer', name: 'cloturer', methods: ['GET'])]
    public function cloturer(Sortie $sortie, EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser();

        if (!$user || $sortie->getOrganisateur() !== $user) {
            $this->addFlash('error', 'Seul l’organisateur peut clôturer cette sortie.');
            return $this->redirectToRoute('sorties_details', ['id' => $sortie->getId()]);
        }

        //Récupération de l'état par noEtat
        $etatCloture = $em->getRepository(Etat::class)->findOneBy(['noEtat' => 3]);
        if (!$etatCloture) {
            $this->addFlash('error', 'État "Clôturée" introuvable dans la base.');
            return $this->redirectToRoute('sorties_details', ['id' => $sortie->getId()]);
        }

        $sortie->setEtat($etatCloture);
        $em->flush();

        $this->addFlash('success', 'La sortie a été clôturée avec succès ✅');

        return $this->redirectToRoute('sorties_details', ['id' => $sortie->getId()]);
    }


    #[Route('/{id}/publier', name: 'publier', methods: ['GET'])]
    public function publier(Sortie $sortie, EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser();

        if (!$user || $sortie->getOrganisateur() !== $user) {
            $this->addFlash('error', 'Seul l’organisateur peut publier cette sortie.');
            return $this->redirectToRoute('sorties_details', ['id' => $sortie->getId()]);
        }

        //Récupération de l'état par noEtat
        $etatOuvert = $em->getRepository(Etat::class)->findOneBy(['noEtat' => 2]);
        if (!$etatOuvert) {
            $this->addFlash('error', 'État "Ouverte" introuvable dans la base.');
            return $this->redirectToRoute('sorties_details', ['id' => $sortie->getId()]);
        }

        $sortie->setEtat($etatOuvert);
        $em->flush();

        $this->addFlash('success', 'La sortie a été publiée avec succès ✅');

        return $this->redirectToRoute('sorties_details', ['id' => $sortie->getId()]);
    }


    #[Route('/{id}/annuler', name: 'annuler', methods: ['GET'])]
    public function annuler(Sortie $sortie, EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser();

        if (!$user || $sortie->getOrganisateur() !== $user || !$user->isAdministrateur()) {
            $this->addFlash('error', 'Seul l’organisateur ou l\'administrateur peut annuler cette sortie.');
            return $this->redirectToRoute('sorties_details', ['id' => $sortie->getId()]);
        }

        //Récupération de l'état par noEtat
        $etatOuvert = $em->getRepository(Etat::class)->findOneBy(['noEtat' => 6]);
        if (!$etatOuvert) {
            $this->addFlash('error', 'État "Ouverte" introuvable dans la base.');
            return $this->redirectToRoute('sorties_details', ['id' => $sortie->getId()]);
        }

        $sortie->setEtat($etatOuvert);
        $em->flush();

        $this->addFlash('success', 'La sortie a été annulée avec succès ✅');

        return $this->redirectToRoute('sorties_details', ['id' => $sortie->getId()]);
    }




    #[Route('/{id}/desinscription', name: 'desinscription', methods: ['GET'])]
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

            $this->addFlash('success', 'Vous avez été désinscrit avec succès ✅');
        }

        return $this->redirectToRoute('sorties_details', ['id' => $sortie->getId()]);
    }





    #[Route('/{id}/inscription', name: 'inscription', methods: ['GET'])]
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

    #[Route('/{id}/modifier', name: 'modifier', requirements: ['id' => '\d+'], methods: ['GET','POST'])]
    public function modifier(int $id, Request $request, EntityManagerInterface $em, FileUploaderSortie $fileUploader): Response
    {
        $sortie = $em->getRepository(Sortie::class)->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException('Sortie non trouvée.');
        }

        $form = $this->createForm(SortiesFormType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('urlPhoto')->getData();
            if($imageFile){
                $sortie->setUrlPhoto($fileUploader->upload($imageFile));
            }

            $em->flush();
            $this->addFlash('success', 'Sortie modifiée avec succès ✅');

            return $this->redirectToRoute('sorties_details', ['id' => $sortie->getId()]);
        }

        return $this->render('sorties/modifier.html.twig', [
            'form' => $form->createView(),
            'sortie' => $sortie,
        ]);
    }

    #[Route('/{id}', name: 'details', requirements: ['id' => '\d+'], methods: ['GET'])]
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

    #[Route('/', name: 'liste', methods: ['GET','POST'])]
    public function index(EntityManagerInterface $em): Response
    {
        $sorties = $em->getRepository(Sortie::class)->findAll();

        return $this->render('sorties/liste.html.twig', [
            'sorties' => $sorties,
        ]);
    }

    #[Route('/ajouter', name: 'ajouter', methods: ['GET','POST'])]
    public function ajouter(Request $request, EntityManagerInterface $em, FileUploaderSortie $fileUploader): Response
    {

        $sortie = new Sortie();
        $form = $this->createForm(SortiesFormType::class, $sortie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('urlPhoto')->getData();
            if($imageFile){
                $sortie->setUrlPhoto($fileUploader->upload($imageFile));
            }

            $sortie->setOrganisateur($this->getUser());

            //Récupération de l'état par noEtat
            $etatOuvert = $em->getRepository(Etat::class)->findOneBy(['noEtat' => 1]);
            if (!$etatOuvert) {
                $this->addFlash('error', 'État "Créée" introuvable dans la base.');
                return $this->redirectToRoute('sorties_details', ['id' => $sortie->getId()]);
            }
            $sortie->setEtat($etatOuvert);

            $em->persist($sortie);

            $em->flush();

            $this->addFlash('success', 'Sortie créée avec succès ✅');

            return $this->redirectToRoute('sorties_liste');
        }

        return $this->render('sorties/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/supprimer', name: 'supprimer', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function supprimer(int $id, EntityManagerInterface $em): Response
    {
        $sortie = $em->getRepository(Sortie::class)->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException('Sortie non trouvée.');
        }

        $em->remove($sortie);
        $em->flush();
        $this->addFlash('success', 'Sortie modifiée avec succès ✅');

        return $this->redirectToRoute('sorties_liste');

    }
}
