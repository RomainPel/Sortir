<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginControllerTest extends WebTestCase
{
    public function testAccesPageConnexion(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Connexion');
    }

    public function testAccesPageDéconnexion(): void
    {
        $client = static::createClient();
        $client->request('GET', '/logout');

        $this->assertResponseIsSuccessful();
    }
}


