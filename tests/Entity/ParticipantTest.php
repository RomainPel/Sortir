<?php

namespace App\Tests\Entity;

use App\Entity\Participant;
use PHPUnit\Framework\TestCase;

class ParticipantTest extends TestCase
{
    public function testSetterNom(): void
    {
        $sortie = new Participant();
        $sortie->setNom('test');
        $this->assertEquals('test', $sortie->getNom());
    }
}

