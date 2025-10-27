<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Etat;
use App\Form\FiltreSortieFormType;
use App\Form\SortiesFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\FileUploaderSortie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sorties', name: 'sorties_')]
final class SortiesController extends AbstractController
{
    // ------------------------- LISTE DES SORTIES -------------------------
    #[Route('/', name: 'liste', methods: ['GET','POST'])]
    public function index(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser();
        $repo = $em->getRepository(Sortie::class);

        $qb = $repo->createQueryBuilder('s')
            ->leftJoin('s.siteOrganisateur', 'site')
            ->leftJoin('s.etat', 'etat')
            ->leftJoin('s.participants', 'p')
            ->addSelect('site', 'etat', 'p')
            ->orderBy('s.dateDebut', 'ASC');

        // Recherche par nom de sortie
        if ($q = $request->query->get('q')) {
            $qb->andWhere('LOWER(s.nom) LIKE LOWER(:q)')
                ->setParameter('q', "%$q%");
        }

        // Filtre par site
        if ($site = $request->query->get('site')) {
            $qb->andWhere('LOWER(site.nomSite) = LOWER(:site)')
                ->setParameter('site', $site);
        }

        // Filtre par date
        if ($dateDebut = $request->query->get('dateDebut')) {
            $qb->andWhere('s.dateDebut >= :dateDebut')
                ->setParameter('dateDebut', new \DateTime($dateDebut));
        }
        if ($dateFin = $request->query->get('dateFin')) {
            $qb->andWhere('s.dateDebut <= :dateFin')
                ->setParameter('dateFin', new \DateTime($dateFin));
        }

        // Filtre par inscription
        if ($request->query->get('inscrit')) {
            $qb->andWhere(':user MEMBER OF s.participants')
                ->setParameter('user', $user);
        }
        if ($request->query->get('nonInscrit')) {
            $qb->andWhere(':user NOT MEMBER OF s.participants')
                ->setParameter('user', $user);
        }

        // Filtre organisateur
        if ($request->query->get('organisateur')) {
            $qb->andWhere('s.organisateur = :user')
                ->setParameter('user', $user);
        }

        // Filtre sorties terminées
        if ($request->query->get('terminee')) {
            $qb->andWhere('etat.libelle = :terminee')
                ->setParameter('terminee', 'Terminée');
        }

        $sorties = $qb->getQuery()->getResult();
        $sites = $em->getRepository(\App\Entity\Site::class)->findAll();

        return $this->render('sorties/liste.html.twig', [
            'sorties' => $sorties,
            'sites' => $sites,
        ]);
    }

    // ------------------------- AJOUTER UNE SORTIE -------------------------
    #[Route('/ajouter', name: 'ajouter', methods: ['GET','POST'])]
    public function ajouter(Request $request, EntityManagerInterface $em, FileUploaderSortie $fileUploader): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortiesFormType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($imageFile = $form->get('urlPhoto')->getData()) {
                $sortie->setUrlPhoto($fileUploader->upload($imageFile));
            }

            $sortie->setOrganisateur($this->getUser());

            $etatCreer = $em->getRepository(Etat::class)->findOneBy(['noEtat' => 1]);
            if (!$etatCreer) {
                $this->addFlash('error', 'État "Créée" introuvable.');
                return $this->redirectToRoute('sorties_liste');
            }
            $sortie->setEtat($etatCreer);

            $em->persist($sortie);
            $em->flush();

            $this->addFlash('success', 'Sortie créée avec succès ✅');
            return $this->redirectToRoute('sorties_liste');
        }

        return $this->render('sorties/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // ------------------------- DÉTAILS -------------------------
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

    // ------------------------- INSCRIPTION / DÉSINSCRIPTION -------------------------
    #[Route('/{id}/inscription', name: 'inscription', methods: ['GET'])]
    public function inscription(Sortie $sortie, EntityManagerInterface $em, Security $security): Response
    {
        $participant = $security->getUser();
        if (!$participant) {
            $this->addFlash('error', 'Vous devez être connecté pour vous inscrire.');
            return $this->redirectToRoute('app_login');
        }

        if ($sortie->getParticipants()->contains($participant)) {
            $this->addFlash('warning', 'Vous êtes déjà inscrit à cette sortie.');
        } elseif ($sortie->getParticipants()->count() >= $sortie->getNbinscriptionmax()) {
            $this->addFlash('danger', 'La sortie est complète.');
        } else {
            $sortie->addParticipant($participant);
            $em->flush();
            $this->addFlash('success', 'Inscription réussie ✅');
        }

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

        if (!$sortie->getParticipants()->contains($participant)) {
            $this->addFlash('warning', 'Vous n’êtes pas inscrit à cette sortie.');
        } else {
            $sortie->removeParticipant($participant);
            $em->flush();
            $this->addFlash('success', 'Vous avez été désinscrit avec succès ✅');
        }

        return $this->redirectToRoute('sorties_details', ['id' => $sortie->getId()]);
    }

    // ------------------------- MODIFIER -------------------------
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
            if ($imageFile = $form->get('urlPhoto')->getData()) {
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

    // ------------------------- GESTION DES ÉTATS -------------------------
    #[Route('/{id}/publier', name: 'publier', methods: ['GET'])]
    public function publier(Sortie $sortie, EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser();
        if (!$user || $sortie->getOrganisateur() !== $user) {
            $this->addFlash('error', 'Seul l’organisateur peut publier cette sortie.');
            return $this->redirectToRoute('sorties_details', ['id' => $sortie->getId()]);
        }

        $etat = $em->getRepository(Etat::class)->findOneBy(['noEtat' => 2]);
        if (!$etat) {
            $this->addFlash('error', 'État "Ouverte" introuvable.');
            return $this->redirectToRoute('sorties_details', ['id' => $sortie->getId()]);
        }

        $sortie->setEtat($etat);
        $em->flush();
        $this->addFlash('success', 'La sortie a été publiée avec succès ✅');

        return $this->redirectToRoute('sorties_details', ['id' => $sortie->getId()]);
    }

    #[Route('/{id}/cloturer', name: 'cloturer', methods: ['GET'])]
    public function cloturer(Sortie $sortie, EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser();
        if (!$user || $sortie->getOrganisateur() !== $user) {
            $this->addFlash('error', 'Seul l’organisateur peut clôturer cette sortie.');
            return $this->redirectToRoute('sorties_details', ['id' => $sortie->getId()]);
        }

        $etat = $em->getRepository(Etat::class)->findOneBy(['noEtat' => 3]);
        if (!$etat) {
            $this->addFlash('error', 'État "Clôturée" introuvable.');
            return $this->redirectToRoute('sorties_details', ['id' => $sortie->getId()]);
        }

        $sortie->setEtat($etat);
        $em->flush();
        $this->addFlash('success', 'La sortie a été clôturée avec succès ✅');

        return $this->redirectToRoute('sorties_details', ['id' => $sortie->getId()]);
    }

    #[Route('/{id}/annuler', name: 'annuler', methods: ['GET'])]
    public function annuler(Sortie $sortie, EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser();
        if (!$user || ($sortie->getOrganisateur() !== $user && !$
                $user->isAdministrateur())) {
            $this->addFlash('error', 'Seul l’organisateur ou l’administrateur peut annuler cette sortie.');
            return $this->redirectToRoute('sorties_details', ['id' => $sortie->getId()]);
        }
        $etat = $em->getRepository(Etat::class)->findOneBy(['noEtat' => 6]);
        if (!$etat) {
            $this->addFlash('error', 'État "Annulée" introuvable.');
            return $this->redirectToRoute('sorties_details', ['id' => $sortie->getId()]);
        }

        $sortie->setEtat($etat);
        $em->flush();
        $this->addFlash('success', 'La sortie a été annulée avec succès ✅');

        return $this->redirectToRoute('sorties_details', ['id' => $sortie->getId()]);
    }
    }

