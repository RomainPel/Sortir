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

    public function testFormValide(): void
    {
        $client = static::createClient();

        $client->request('GET', '/login');

        $client->submitForm("Connexion", [
            '_username' => 'test',
            '_password' => '!Test01!',
        ]);

        $this->assertResponseIsSuccessful();
    }

    public function testFormInvalideSiPasDePseudo(): void
    {
        $client = static::createClient();

        $client->request('GET', '/login');

        $client->submitForm("Connexion", [
            '_username' => '',
            '_password' => '!Test01!',
        ]);

        $this->assertEquals(422, $client->getResponse()->getStatusCode());
    }
}


