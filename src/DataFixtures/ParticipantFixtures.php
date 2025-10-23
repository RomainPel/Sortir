<?php

namespace App\DataFixtures;

use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ParticipantFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasher) {}

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $sites = $manager->getRepository(Site::class)->findAll();

        $admin = new Participant();
        $admin->setPseudo('admin');
        $admin->setNom('admin');
        $admin->setPrenom('admin');
        $admin->setMail('admin@sorties.com');
        $admin->setTelephone('0606060606');
        $admin->setMotDePasse($this->userPasswordHasher->hashPassword($admin, 'admin'));
        $admin->setAdministrateur(true);
        $admin->setSite($faker->randomElement($sites));
        //$admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);
        $this->addReference('admin', $admin);

        for ($i = 0; $i <= 10; $i++) {
            $participant = new Participant();
            $participant->setPseudo($faker->userName);
            $participant->setNom($faker->name);
            $participant->setPrenom($faker->firstName);
            $participant->setMail($faker->email);
            $participant->setTelephone($faker->phoneNumber);
            $participant->setMotDePasse($this->userPasswordHasher->hashPassword($participant, 'password'));
            $participant->setAdministrateur(false);
            //$participant->setSite($this->getReference('site'.mt_rand(1,4),Site::class));
            $participant->setSite($faker->randomElement($sites));
            //$this->addSorties($participant);
            $manager->persist($participant);
            $this->addReference('participant'.$i, $participant);
        }
        $manager->flush();
    }

    /*private function addSorties(Participant $participant) :void{
        for($i=0;$i<=mt_rand(0,5);$i++){
            $sortie=$this->getReference('sortie'.rand(1,10),Sortie::class);
            $participant->addSortieOrganise($sortie);
        }
    }*/

    public function getDependencies(): array
    {
        return [SiteFixtures::class];
    }
}
