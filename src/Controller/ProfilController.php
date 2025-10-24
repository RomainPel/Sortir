<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ProfilFormType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/profil', name: 'profil_')]
final class ProfilController extends AbstractController
{
#[Route('/{id}', name: 'view', requirements: ['id' => '\d+'], methods: ['GET'])]
public function detailProfil(int $id, EntityManagerInterface $em): Response
{
    $participant = $em->getRepository(Participant::class)->find($id);

    if (!$participant) {
        throw $this->createNotFoundException('Participant non trouvé');
    }

    return $this->render('profil/view.html.twig', [
        'participant' => $participant,
    ]);
}

#[Route('/{id}/modifier', name: 'edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
public function modifierProfil(Participant $participant, Request $request, EntityManagerInterface $em, FileUploader $fileUploader): Response
{
    if (!$participant) {
        throw $this->createNotFoundException('Participant non trouvé');
    }

    $profilForm = $this->createForm(ProfilFormType::class, $participant);
    $profilForm->handleRequest($request);
    if ($profilForm->isSubmitted() && $profilForm->isValid()) {
        $imageFile = $profilForm->get('photoProfil')->getData();
        if($imageFile){
            $participant->setPhotoProfil($fileUploader->upload($imageFile));
        }

        $em->flush();
        $this->addFlash('success', 'Profil modifié avec succès !');
        return $this->redirectToRoute('profil_view', ['id' => $participant->getId()]);
    }
    return $this->render('profil/edit.html.twig',
        ["form" => $profilForm]
    );
}
}
