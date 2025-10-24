<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleFormType;
use App\Repository\VillesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/villes', name: 'villes_')]
class VilleController extends AbstractController
{

    #[Route('/supprimer/{id}', name: 'supprimer', requirements: ['id' => '\d+'])]
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        $ville = $entityManager->getRepository(Ville::class)->find($id);

        if (!$ville) {
            throw $this->createNotFoundException('Ville non trouvée');
        }

        $entityManager->remove($ville);
        $entityManager->flush();

        $this->addFlash('success', 'Ville supprimée avec succès !');

        return $this->redirectToRoute('villes_liste');
    }






    #[Route('/modifier/{id}', name: 'modifier', requirements: ['id' => '\d+'])]
    public function modifier(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $ville = $entityManager->getRepository(Ville::class)->find($id);

        if (!$ville) {
            throw $this->createNotFoundException('Ville non trouvée');
        }

        $form = $this->createForm(VilleFormType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Ville modifiée avec succès !');
            return $this->redirectToRoute('villes_liste');
        }

        return $this->render('ville/modifier.html.twig', [
            'form' => $form->createView(),
            'ville' => $ville,
        ]);
    }



    #[Route('/', name: 'liste')]
    public function index(VillesRepository $villeRepository, Request $request): Response
    {
        $search = $request->query->get('search');

        if ($search) {
            $villes = $villeRepository->createQueryBuilder('v')
                ->where('v.nomVille LIKE :search')
                ->setParameter('search', '%'.$search.'%')
                ->getQuery()
                ->getResult();
        } else {
            $villes = $villeRepository->findAll();
        }

        return $this->render('ville/liste.html.twig', [
            'villes' => $villes,
            'search' => $search,
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
