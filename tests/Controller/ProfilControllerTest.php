<?php

namespace App\Tests\Controller;

use App\Entity\Participant;
use App\Repository\ParticipantsRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProfilControllerTest extends WebTestCase
{
    public function testRedirectionSiUtilisateurNonConnecte(): void
    {
        $client = static::createClient();
        $client->request('GET', '/profil/1');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        $this->assertRouteSame('app_login');
    }

    public function testAccesPageProfilSiUtilisateurNonConnecte(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getUser());
        $client->request('GET', '/profil/1');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Profil');
    }

    private function getUser(): Participant
    {
        $participantsRepository = static::getContainer()->get(ParticipantsRepository::class);
        return $participantsRepository->findOneBy(['pseudo' => 'admin']);
    }
}

