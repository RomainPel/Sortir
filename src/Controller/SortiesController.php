<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Etat;
use App\Entity\Site;
use App\Form\SortiesFormType;
use App\Service\FileUploaderSortie;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sorties', name: 'sorties_')]
final class SortiesController extends AbstractController
{
    // ------------------------- LISTE DES SORTIES -------------------------
    #[Route('/', name: 'liste', methods: ['GET', 'POST'])]
    public function liste(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        date_default_timezone_set('Europe/Paris');
        $dateJour = new \DateTime();

        // 🔁 Mise à jour automatique des états
        $sorties = $em->getRepository(Sortie::class)->findAll();
        foreach ($sorties as $sortie) {
            $this->updateEtatSortie($sortie, $em, $dateJour);
        }
        $em->flush();

        $user = $security->getUser();
        $repo = $em->getRepository(Sortie::class);

        $qb = $repo->createQueryBuilder('s')
            ->leftJoin('s.siteOrganisateur', 'site')
            ->leftJoin('s.etat', 'etat')
            ->leftJoin('s.participants', 'p')
            ->addSelect('site', 'etat', 'p')
            ->orderBy('s.dateDebut', 'ASC');

        // 🔍 Filtres dynamiques
        if ($q = $request->query->get('q')) {
            $qb->andWhere('LOWER(s.nom) LIKE LOWER(:q)')
                ->setParameter('q', "%$q%");
        }

        if ($site = $request->query->get('site')) {
            $qb->andWhere('LOWER(site.nomSite) = LOWER(:site)')
                ->setParameter('site', $site);
        }

        if ($dateDebut = $request->query->get('dateDebut')) {
            $qb->andWhere('s.dateDebut >= :dateDebut')
                ->setParameter('dateDebut', new \DateTime($dateDebut));
        }

        if ($dateFin = $request->query->get('dateFin')) {
            $qb->andWhere('s.dateDebut <= :dateFin')
                ->setParameter('dateFin', new \DateTime($dateFin));
        }

        if ($request->query->get('inscrit')) {
            $qb->andWhere(':user MEMBER OF s.participants')->setParameter('user', $user);
        }

        if ($request->query->get('nonInscrit')) {
            $qb->andWhere(':user NOT MEMBER OF s.participants')->setParameter('user', $user);
        }

        if ($request->query->get('organisateur')) {
            $qb->andWhere('s.organisateur = :user')->setParameter('user', $user);
        }

        if ($request->query->get('terminee')) {
            $qb->andWhere('etat.noEtat = 5');
        }

        $sorties = $qb->getQuery()->getResult();
        $sites = $em->getRepository(Site::class)->findAll();

        return $this->render('sorties/liste.html.twig', [
            'sorties' => $sorties,
            'sites' => $sites,
        ]);
    }

    // ------------------------- DÉTAILS -------------------------
    #[Route('/{id}', name: 'details', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function detail(int $id, EntityManagerInterface $em): Response
    {
        $sortie = $em->getRepository(Sortie::class)->find($id);
        if (!$sortie) {
            throw $this->createNotFoundException('Sortie non trouvée.');
        }

        $this->updateEtatSortie($sortie, $em, new \DateTime('now', new \DateTimeZone('Europe/Paris')));
        $em->flush();

        return $this->render('sorties/details.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    // ------------------------- AJOUTER -------------------------
    #[Route('/ajouter', name: 'ajouter', methods: ['GET', 'POST'])]
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

    // ------------------------- MODIFIER -------------------------
    #[Route('/{id}/modifier', name: 'modifier', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
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

    // ------------------------- SUPPRIMER -------------------------
    #[Route('/{id}/supprimer', name: 'supprimer', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function supprimer(int $id, EntityManagerInterface $em): Response
    {
        $sortie = $em->getRepository(Sortie::class)->find($id);
        if (!$sortie) {
            throw $this->createNotFoundException('Sortie non trouvée.');
        }

        $em->remove($sortie);
        $em->flush();

        $this->addFlash('success', 'Sortie supprimée avec succès 🗑️');
        return $this->redirectToRoute('sorties_liste');
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
            $this->addFlash('success', 'Désinscription réussie ✅');
        }

        return $this->redirectToRoute('sorties_details', ['id' => $sortie->getId()]);
    }

    // ------------------------- GESTION DES ÉTATS -------------------------
    #[Route('/{id}/publier', name: 'publier', methods: ['GET'])]
    public function publier(Sortie $sortie, EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser();
        if ($sortie->getOrganisateur() !== $user) {
            $this->addFlash('error', 'Seul l’organisateur peut publier cette sortie.');
            return $this->redirectToRoute('sorties_details', ['id' => $sortie->getId()]);
        }

        $etat = $em->getRepository(Etat::class)->findOneBy(['noEtat' => 2]);
        $sortie->setEtat($etat);
        $em->flush();

        $this->addFlash('success', 'La sortie a été publiée avec succès ✅');
        return $this->redirectToRoute('sorties_details', ['id' => $sortie->getId()]);
    }

    #[Route('/{id}/cloturer', name: 'cloturer', methods: ['GET'])]
    public function cloturer(Sortie $sortie, EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser();
        if ($sortie->getOrganisateur() !== $user) {
            $this->addFlash('error', 'Seul l’organisateur peut clôturer cette sortie.');
            return $this->redirectToRoute('sorties_details', ['id' => $sortie->getId()]);
        }

        $etat = $em->getRepository(Etat::class)->findOneBy(['noEtat' => 3]);
        $sortie->setEtat($etat);
        $em->flush();

        $this->addFlash('success', 'Sortie clôturée avec succès ✅');
        return $this->redirectToRoute('sorties_details', ['id' => $sortie->getId()]);
    }

    #[Route('/{id}/annuler', name: 'annuler', methods: ['GET'])]
    public function annuler(Sortie $sortie, EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser();

        // Vérifie si l’utilisateur est admin
        if (!method_exists($user, 'isAdministrateur')) {
            $isAdmin = in_array('ROLE_ADMIN', $user->getRoles());
        } else {
            $isAdmin = $user->isAdministrateur();
        }

        if ($sortie->getOrganisateur() !== $user && !$isAdmin) {
            $this->addFlash('error', 'Seul l’organisateur ou un administrateur peut annuler cette sortie.');
            return $this->redirectToRoute('sorties_details', ['id' => $sortie->getId()]);
        }

        $etat = $em->getRepository(Etat::class)->findOneBy(['noEtat' => 6]);
        $sortie->setEtat($etat);
        $em->flush();

        $this->addFlash('success', 'Sortie annulée avec succès ✅');
        return $this->redirectToRoute('sorties_details', ['id' => $sortie->getId()]);
    }

    // ------------------------- MISE À JOUR DES ÉTATS -------------------------
    private function updateEtatSortie(Sortie $sortie, EntityManagerInterface $em, \DateTime $dateJour): void
    {
        $dateDebut = clone $sortie->getDateDebut();
        $dateCloture = clone $sortie->getDateCloture();
        $dateFin = (clone $dateDebut)->add(new DateInterval('PT' . $sortie->getDuree() . 'M'));

        $etatRepo = $em->getRepository(Etat::class);

        if ($dateJour >= $dateFin) {
            $sortie->setEtat($etatRepo->findOneBy(['noEtat' => 5])); // Passée
        } elseif ($dateJour >= $dateDebut) {
            $sortie->setEtat($etatRepo->findOneBy(['noEtat' => 4])); // En cours
        } elseif ($dateJour >= $dateCloture) {
            $sortie->setEtat($etatRepo->findOneBy(['noEtat' => 3])); // Clôturée
        }
    }
}
