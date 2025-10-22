<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
class LieuFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $villes = $manager->getRepository(Ville::class)->findAll();

        for ($i = 0; $i <= 10; $i++) {
            $lieu = new Lieu();
            $lieu->setNomLieu($faker->city);
            $lieu->setRue($faker->address);
            $lieu->setVille($faker->randomElement($villes));
            $lieu->setLatitude($faker->latitude);
            $lieu->setLongitude($faker->longitude);
            $this->addSorties($lieu);
            $manager->persist($lieu);
        }
        $manager->flush();
    }

    private function addSorties(Lieu $lieu) :void{
        for($i=0;$i<=mt_rand(0,5);$i++){
            $sortie=$this->getReference('sortie'.rand(1,10),Sortie::class);
            $lieu->addSortie($sortie);
        }
    }

    public function getDependencies(): array
    {
        return [VilleFixtures::class];
    }
}
