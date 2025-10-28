<?php

namespace App\Tests\Entity;

use App\Entity\Lieu;
use PHPUnit\Framework\TestCase;

class LieuTest extends TestCase
{
    public function testSetterNom(): void
    {
        $sortie = new Lieu();
        $sortie->setNomLieu('test');
        $this->assertEquals('test', $sortie->getNomLieu());
    }
}

