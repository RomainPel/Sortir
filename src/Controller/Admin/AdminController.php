<?php
// src/Controller/AdminController.php
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminController extends AbstractController
{
#[Route('/admin/import', name: 'admin_import_users')]
public function importUsers(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
{
$file = $request->files->get('file');
if (!$file) {
$this->addFlash('error', 'Aucun fichier envoyé.');
return $this->redirectToRoute('admin_dashboard');
}

if (($handle = fopen($file->getPathname(), 'r')) !== false) {
$header = fgetcsv($handle, 1000, ';'); // première ligne = titres
while (($data = fgetcsv($handle, 1000, ';')) !== false) {
$user = new User();
$user->setNom($data[0]);
$user->setPrenom($data[1]);
$user->setEmail($data[2]);
$user->setRoles(['ROLE_USER']);
$hashedPassword = $hasher->hashPassword($user, $data[3]); // mot de passe dans le CSV
$user->setPassword($hashedPassword);

$em->persist($user);
}
fclose($handle);
$em->flush();
}

$this->addFlash('success', 'Utilisateurs importés avec succès.');
return $this->redirectToRoute('admin_dashboard');
}
}

