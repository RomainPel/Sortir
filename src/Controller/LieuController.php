<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuFormType;
use App\Repository\LieuxRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/lieux', name: 'lieux_')]
class LieuController extends AbstractController
{
    #[Route('/', name: 'liste')]
    public function index(LieuxRepository $lieuRepository, Request $request): Response
    {
        $search = $request->query->get('search');

        if ($search) {
            $lieux = $lieuRepository->createQueryBuilder('l')
                ->where('l.nomLieu LIKE :search')
                ->setParameter('search', '%'.$search.'%')
                ->getQuery()
                ->getResult();
        } else {
            $lieux = $lieuRepository->findAll();
        }

        return $this->render('lieu/liste.html.twig', [
            'lieux' => $lieux,
            'search' => $search, // pour pré-remplir le champ de recherche
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

    #[Route('/infos/{id}', name: '_infos', methods: ['GET'])]
    public function getLieuInfos(LieuxRepository $lieuRepository, int $id): JsonResponse
    {
        $lieu = $lieuRepository->find($id);

        if (!$lieu) {
            return new JsonResponse(['error' => 'Lieu non trouvé'], 404);
        }

        if ($lieu->getVille()) {
            return new JsonResponse([
                'rue' => $lieu->getRue(),
                'ville' => $lieu->getVille()->getNomVille(),
                'codePostal' => $lieu->getVille()->getCodePostal(),
                'latitude' => $lieu->getLatitude(),
                'longitude' => $lieu->getLongitude(),
            ]);
        } else {
            return new JsonResponse([
                'rue' => $lieu->getRue(),
                'latitude' => $lieu->getLatitude(),
                'longitude' => $lieu->getLongitude(),
            ]);
        }
    }
}
