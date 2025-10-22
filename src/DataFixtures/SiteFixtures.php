<?php

namespace App\DataFixtures;

use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SiteFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $site1 = new Site();
        $site1->setNomSite(': SAINT HERBLAIN');
        $this->addParticipants($site1);
        $this->addSorties($site1);
        $manager->persist($site1);
        $this->addReference('site1', $site1);

        $site2 = new Site();
        $site2->setNomSite('CHARTRES DE BRETAGNE');
        $this->addParticipants($site2);
        $this->addSorties($site2);
        $manager->persist($site2);
        $this->addReference('site2', $site2);

        $site3 = new Site();
        $site3->setNomSite('LA ROCHE SUR YON');
        $this->addParticipants($site3);
        $this->addSorties($site3);
        $manager->persist($site3);
        $this->addReference('site3', $site3);


        $site4 = new Site();
        $site4->setNomSite('QUIMPER');
        $this->addParticipants($site4);
        $this->addSorties($site4);
        $manager->persist($site4);
        $this->addReference('site4', $site4);


        $manager->flush();
    }

    private function addParticipants(Site $site) :void{
        for($i=0;$i<=mt_rand(0,5);$i++){
            $participant=$this->getReference('participant'.rand(1,10),Participant::class);
            $site->addSortie($participant);
        }
    }

    private function addSorties(Site $site) :void{
        for($i=0;$i<=mt_rand(0,5);$i++){
            $sortie=$this->getReference('sortie'.rand(1,10),Sortie::class);
            $site->addSortie($sortie);
        }
    }
}


