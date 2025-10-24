<?php

namespace App\DataFixtures;

use App\Entity\Site;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SiteFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $site1 = new Site();
        $site1->setNomSite('SAINT HERBLAIN');
        $manager->persist($site1);
        $this->addReference('site1', $site1);

        $site2 = new Site();
        $site2->setNomSite('CHARTRES DE BRETAGNE');
        $manager->persist($site2);
        $this->addReference('site2', $site2);

        $site3 = new Site();
        $site3->setNomSite('LA ROCHE SUR YON');
        $manager->persist($site3);
        $this->addReference('site3', $site3);


        $site4 = new Site();
        $site4->setNomSite('QUIMPER');
        $manager->persist($site4);
        $this->addReference('site4', $site4);


        $manager->flush();
    }
}


