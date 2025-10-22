<?php

namespace App\DataFixtures;

use App\Entity\Participant;
use App\Entity\Site;
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
        //$admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        for ($i = 0; $i <= 10; $i++) {
            $participant = new Participant();
            $participant->setPseudo($faker->userName);
            $participant->setNom($faker->name);
            $participant->setPrenom($faker->firstName);
            $participant->setMail($faker->email);
            $participant->setTelephone($faker->phoneNumber);
            $participant->setMotDePasse($this->userPasswordHasher->hashPassword($participant, 'password'));
            $participant->setSite($faker->randomElement($sites));
            $manager->persist($participant);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [SiteFixtures::class];
    }
}
