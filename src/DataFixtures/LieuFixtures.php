<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
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
            $manager->persist($lieu);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [VilleFixtures::class];
    }
}
