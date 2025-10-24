<?php

namespace App\Controller;

use App\Entity\Site;
use App\Form\SiteFormType;
use App\Repository\SitesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sites', name: 'sites_')]
class SiteController extends AbstractController
{
    #[Route('/supprimer/{id}', name: 'supprimer', requirements: ['id' => '\d+'])]
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        $site = $entityManager->getRepository(Site::class)->find($id);

        if (!$site) {
            throw $this->createNotFoundException('Site non trouvé');
        }

        $entityManager->remove($site);
        $entityManager->flush();

        $this->addFlash('success', 'Site supprimé avec succès !');

        return $this->redirectToRoute('sites_liste');
    }

    #[Route('/modifier/{id}', name: 'modifier', requirements: ['id' => '\d+'])]
    public function modifier(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $site = $entityManager->getRepository(Site::class)->find($id);

        if (!$site) {
            throw $this->createNotFoundException('Site non trouvé');
        }

        $form = $this->createForm(SiteFormType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Site modifié avec succès !');
            return $this->redirectToRoute('sites_liste');
        }

        return $this->render('site/modifier.html.twig', [
            'form' => $form->createView(),
            'site' => $site,
        ]);
    }

    #[Route('/', name: 'liste')]
    public function index(SitesRepository $siteRepository, Request $request): Response
    {
        $search = $request->query->get('search');

        if ($search) {
            $sites = $siteRepository->createQueryBuilder('s')
                ->where('s.nomSite LIKE :search')
                ->setParameter('search', '%'.$search.'%')
                ->getQuery()
                ->getResult();
        } else {
            $sites = $siteRepository->findAll();
        }

        return $this->render('site/liste.html.twig', [
            'sites' => $sites,
            'search' => $search, // pour pré-remplir le champ de recherche
        ]);
    }


    #[Route('/ajouter', name: 'ajouter')]
    public function ajouter(Request $request, EntityManagerInterface $entityManager): Response
    {
        $site = new Site();
        $form = $this->createForm(SiteFormType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($site);
            $entityManager->flush();

            $this->addFlash('success', 'Site ajouté avec succès !');

            return $this->redirectToRoute('sites_liste');
        }

        return $this->render('site/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

