<?php

namespace App\Tests\Entity;

use App\Entity\Site;
use PHPUnit\Framework\TestCase;

class SiteTest extends TestCase
{
    public function testSetterNom(): void
    {
        $sortie = new Site();
        $sortie->setNomSite('test');
        $this->assertEquals('test', $sortie->getNomSite());
    }
}

