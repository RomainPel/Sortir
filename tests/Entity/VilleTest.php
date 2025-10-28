<?php

namespace App\Tests\Entity;

use App\Entity\Ville;
use PHPUnit\Framework\TestCase;

class VilleTest extends TestCase
{
    public function testSetterNom(): void
    {
        $sortie = new Ville();
        $sortie->setNomVille('test');
        $this->assertEquals('test', $sortie->getNomVille());
    }
}

