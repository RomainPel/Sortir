<?php

namespace App\Controller\Admin;

use App\Entity\Participant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminImportController extends AbstractController
{
    #[Route('/admin/import', name: 'admin_import')]
    public function import(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $message = null;

        if ($request->isMethod('POST')) {
            $file = $request->files->get('csv_file');

            if ($file && $file->getClientOriginalExtension() === 'csv') {
                $filename = uniqid() . '.csv';

                try {
                    $file->move($this->getParameter('kernel.project_dir') . '/public/uploads', $filename);
                    $filepath = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $filename;

                    if (($handle = fopen($filepath, 'r')) !== false) {
                        // Ignore header line
                        fgetcsv($handle, 1000, ';');

                        while (($data = fgetcsv($handle, 1000, ';')) !== false) {
                            [$pseudo, $nom, $prenom, $telephone, $mail, $password] = $data;

                            $participant = new Participant();
                            $participant->setPseudo($pseudo);
                            $participant->setNom($nom);
                            $participant->setPrenom($prenom);
                            $participant->setTelephone($telephone);
                            $participant->setMail($mail);

                            // Hash du mot de passe
                            $hashedPassword = $passwordHasher->hashPassword($participant, $password);
                            $participant->setMotPasse($hashedPassword);

                            // Valeurs par défaut (si ton entité les a)
                            $participant->setActif(true);

                            $entityManager->persist($participant);
                        }

                        fclose($handle);
                        $entityManager->flush();

                        $message = ['success', 'Importation réussie 🎉'];
                    }
                } catch (FileException $e) {
                    $message = ['error', 'Erreur lors du téléchargement du fichier.'];
                }
            } else {
                $message = ['error', 'Veuillez importer un fichier CSV valide.'];
            }
        }

        return $this->render('admin/import_users.html.twig', [
            'message' => $message,
        ]);
    }
}
