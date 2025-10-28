<?php

namespace App\Tests\Entity;

use App\Entity\Etat;
use PHPUnit\Framework\TestCase;

class EtatTest extends TestCase
{
    public function testSetterLibelle(): void
    {
        $sortie = new Etat();
        $sortie->setLibelle('test');
        $this->assertEquals('test', $sortie->getLibelle());
    }
}
