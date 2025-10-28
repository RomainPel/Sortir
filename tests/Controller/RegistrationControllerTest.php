<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    public function testAccesPageConnexion(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Connexion');
    }

    public function testFormValide(): void
    {
        $client = static::createClient();

        $client->request('GET', '/register');

        $client->submitForm("Enregistrer", [
            'registration_form[pseudo]' => 'test',
            'registration_form[nom]' => 'test',
            'registration_form[prenom]' => 'test',
            'registration_form[telephone]' => '0606060606',
            'registration_form[mail]' => 'test@test.com',
            'registration_form[plainPassword]' => 'Pa$$w0rd',
        ]);

        $this->assertResponseIsSuccessful();
    }
}
