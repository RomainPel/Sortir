<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use App\Entity\Sortie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EtatFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $etat1 = new Etat();
        $etat1->setLibelle('Créée');
        $this->addSorties($etat1);
        $manager->persist($etat1);
        $this->addReference('etat1', $etat1);

        $etat2 = new Etat();
        $etat2->setLibelle('Ouverte');
        $this->addSorties($etat2);
        $manager->persist($etat2);
        $this->addReference('etat2', $etat2);

        $etat3 = new Etat();
        $etat3->setLibelle('Clôturée');
        $this->addSorties($etat3);
        $manager->persist($etat3);
        $this->addReference('etat3', $etat3);


        $etat4 = new Etat();
        $etat4->setLibelle('Activité en cours');
        $this->addSorties($etat4);
        $manager->persist($etat4);
        $this->addReference('etat4', $etat4);

        $etat5 = new Etat();
        $etat5->setLibelle('Passée');
        $this->addSorties($etat5);
        $manager->persist($etat5);
        $this->addReference('etat5', $etat5);

        $etat6 = new Etat();
        $etat6->setLibelle('Annulée');
        $this->addSorties($etat6);
        $manager->persist($etat6);
        $this->addReference('etat6', $etat6);


        $manager->flush();
    }

    private function addSorties(Etat $etat) :void{
        for($i=0;$i<=mt_rand(0,5);$i++){
            $sortie=$this->getReference('sortie'.rand(1,10),Sortie::class);
            $etat->addSortie($sortie);
        }
    }
}

