<?php

namespace App\Tests\Entity;

use App\Entity\Sortie;
use PHPUnit\Framework\TestCase;

class SortieTest extends TestCase
{
    public function testSetterNom(): void
    {
        $sortie = new Sortie();
        $sortie->setNom('test');
        $this->assertEquals('test', $sortie->getNom());
    }
}
