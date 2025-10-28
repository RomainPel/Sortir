<?php

namespace App\Tests\Controller;

use App\Entity\Participant;
use App\Repository\ParticipantsRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SortieControllerTest extends WebTestCase
{
    public function testSiPageConnexionExiste(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Connexion');
    }

    //-------------------TESTS ACCES PAGES---------------------------
    public function testRedirectionSiUtilisateurNonConnecte(): void
    {
        $client = static::createClient();
        $client->request('GET', '/sorties/');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        $this->assertRouteSame('app_login');
    }

    public function testAccesPageSortiesSiUtilisateurNonConnecte(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getUser());
        $client->request('GET', '/sorties/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Liste des Sorties');
    }

    //-------------------TESTS FORMULAIRES---------------------------

    public function testFormValide(): void
    {
        $client = static::createClient();

        $client->loginUser($this->getUser());
        $client->request('GET', '/sorties/ajouter');

        $client->submitForm("Enregistrer", [
            'sorties_form[nom]' => 'test',
            'sorties_form[datedebut]' => '2025-11-10T14:00',
            'sorties_form[duree]' => '120',
            'sorties_form[datecloture]' => '2025-11-14T14:00',
            'sorties_form[lieu]' => '2',
            'sorties_form[nbinscriptionmax]' => '10',
            'sorties_form[descriptioninfos]' => 'description test',
            'sorties_form[urlPhoto]' => 'test.png',
        ]);

        $this->assertResponseIsSuccessful();
    }

    public function testFormInvalideSiPasDeNom(): void
    {
        $client = static::createClient();

        $client->loginUser($this->getUser());
        $client->request('GET', '/sorties/ajouter');

        $client->submitForm("Enregistrer", [
            'sorties_form[nom]' => '',
            'sorties_form[datedebut]' => '2025-11-10T14:00',
            'sorties_form[duree]' => '120',
            'sorties_form[datecloture]' => '2025-11-14T14:00',
            'sorties_form[lieu]' => '2',
            'sorties_form[nbinscriptionmax]' => '10',
            'sorties_form[descriptioninfos]' => 'description test',
            'sorties_form[urlPhoto]' => 'test.png',
        ]);

        $this->assertEquals(500, $client->getResponse()->getStatusCode());
    }

    public function testFormInvalideSiNomTropLong(): void
    {
        $client = static::createClient();

        $client->loginUser($this->getUser());
        $client->request('GET', '/sorties/ajouter');

        $nom = str_repeat('x', 31);

        $client->submitForm("Enregistrer", [
            'sorties_form[nom]' => $nom,
            'sorties_form[datedebut]' => '2025-11-10T14:00',
            'sorties_form[duree]' => '120',
            'sorties_form[datecloture]' => '2025-11-14T14:00',
            'sorties_form[lieu]' => '2',
            'sorties_form[nbinscriptionmax]' => '10',
            'sorties_form[descriptioninfos]' => 'description test',
            'sorties_form[urlPhoto]' => 'test.png',
        ]);

        $this->assertEquals(500, $client->getResponse()->getStatusCode());
    }

    public function testFormInvalideSiDureeNegative(): void
    {
        $client = static::createClient();

        $client->loginUser($this->getUser());
        $client->request('GET', '/sorties/ajouter');

        $client->submitForm("Enregistrer", [
            'sorties_form[nom]' => '',
            'sorties_form[datedebut]' => '2025-11-10T14:00',
            'sorties_form[duree]' => '-2',
            'sorties_form[datecloture]' => '2025-11-14T14:00',
            'sorties_form[lieu]' => '2',
            'sorties_form[nbinscriptionmax]' => '10',
            'sorties_form[descriptioninfos]' => 'description test',
            'sorties_form[urlPhoto]' => 'test.png',
        ]);

        $this->assertEquals(500, $client->getResponse()->getStatusCode());
    }

    public function testFormInvalideSiNbinscriptionmaxNegatif(): void
    {
        $client = static::createClient();

        $client->loginUser($this->getUser());
        $client->request('GET', '/sorties/ajouter');

        $client->submitForm("Enregistrer", [
            'sorties_form[nom]' => '',
            'sorties_form[datedebut]' => '2025-11-10T14:00',
            'sorties_form[duree]' => '120',
            'sorties_form[datecloture]' => '2025-11-14T14:00',
            'sorties_form[lieu]' => '2',
            'sorties_form[nbinscriptionmax]' => '-3',
            'sorties_form[descriptioninfos]' => 'description test',
            'sorties_form[urlPhoto]' => 'test.png',
        ]);

        $this->assertEquals(500, $client->getResponse()->getStatusCode());
    }

    private function getUser(): Participant
    {
        $participantsRepository = static::getContainer()->get(ParticipantsRepository::class);
        return $participantsRepository->findOneBy(['pseudo' => 'admin']);
    }
}
