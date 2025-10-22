<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SortieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $participants = $manager->getRepository(Participant::class)->findAll();
        $sites = $manager->getRepository(Site::class)->findAll();
        $etats = $manager->getRepository(Etat::class)->findAll();
        $lieux = $manager->getRepository(Lieu::class)->findAll();

        for ($i = 0; $i <= 10; $i++) {
            $sortie = new Sortie();
            $sortie->setNom($faker->word);
            $sortie->setDescriptioninfos($faker->realText());
            $sortie->setDatedebut($faker->dateTimeBetween('-3 months', 'now'));
            $sortie->setDatecloture($faker->dateTimeBetween($sortie->getDatedebut(), '+3 months'));
            $sortie->setDuree($faker->randomFloat());
            $sortie->setNbinscriptionmax($faker->randomNumber());
            $sortie->setOrganisateur($faker->randomElement($participants));
            $sortie->setSiteOrganisateur($faker->randomElement($sites));
            $sortie->setEtat($faker->randomElement($etats));
            $sortie->setLieu($faker->randomElement($lieux));
            $this->addParticipants($sortie);
            $manager->persist($sortie);
            $this->addReference('sortie'.$i, $sortie);
        }
        $manager->flush();
    }

    private function addParticipants(Sortie $sortie) :void{
        for($i=0;$i<=mt_rand(0,5);$i++){
            $participant=$this->getReference('participant'.rand(1,10),Participant::class);
            $sortie->addParticipant($participant);
        }
    }

    public function getDependencies(): array
    {
        return [ParticipantFixtures::class, SiteFixtures::class, EtatFixtures::class, SortieFixtures::class];
    }
}
