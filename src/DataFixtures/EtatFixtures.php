<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EtatFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $etat1 = new Etat();
        $etat1->setLibelle('Créée');
        $manager->persist($etat1);
        $this->addReference('etat1', $etat1);

        $etat2 = new Etat();
        $etat2->setLibelle('Ouverte');
        $manager->persist($etat2);
        $this->addReference('etat2', $etat2);

        $etat3 = new Etat();
        $etat3->setLibelle('Clôturée');
        $manager->persist($etat3);
        $this->addReference('etat3', $etat3);


        $etat4 = new Etat();
        $etat4->setLibelle('Activité en cours');
        $manager->persist($etat4);
        $this->addReference('etat4', $etat4);

        $etat5 = new Etat();
        $etat5->setLibelle('Passée');
        $manager->persist($etat5);
        $this->addReference('etat5', $etat5);

        $etat6 = new Etat();
        $etat6->setLibelle('Annulée');
        $manager->persist($etat6);
        $this->addReference('etat6', $etat6);


        $manager->flush();
    }
}

