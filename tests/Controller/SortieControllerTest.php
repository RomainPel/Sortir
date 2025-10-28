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
//    public function testFormIsInvalidIfNoCategory(): void
//    {
//        $client = static::createClient();
//
//        $client->loginUser($this->getUser());
//        $client->request('GET', '/wishes/create');
//
//        $client->submitForm("Create", [
//            'wish[title]' => 'Test',
//            'wish[description]' => 'description test',
//            'wish[category]' => ''
//        ]);
//
//        $this->assertEquals(422, $client->getResponse()->getStatusCode());
//    }
//    public function testFormIsInvalidIfTitleHaveTooSmallLength(): void
//    {
//        $client = static::createClient();
//
//        $client->loginUser($this->getUser());
//        $client->request('GET', '/wishes/create');
//
//        $client->submitForm("Create", [
//            'wish[title]' => 'x',
//            'wish[description]' => 'description test',
//            'wish[category]' => '10'
//        ]);
//
//        $this->assertEquals(422, $client->getResponse()->getStatusCode());
//    }
//    public function testFormIsInvalidIfTitleHaveTooBigLength(): void
//    {
//        $client = static::createClient();
//
//        $client->loginUser($this->getUser());
//        $client->request('GET', '/wishes/create');
//
//        $title = str_repeat('x', 256);
//
//        $client->submitForm("Create", [
//            'wish[title]' => $title,
//            'wish[description]' => 'description test',
//            'wish[category]' => '10'
//        ]);
//
//        $this->assertEquals(422, $client->getResponse()->getStatusCode());
//    }
//    public function testFormIsInvalidIfDescriptionHaveTooSmallLength(): void
//    {
//        $client = static::createClient();
//
//        $client->loginUser($this->getUser());
//        $client->request('GET', '/wishes/create');
//
//        $client->submitForm("Create", [
//            'wish[title]' => 'Test',
//            'wish[description]' => 'x',
//            'wish[category]' => '10'
//        ]);
//
//        $this->assertEquals(422, $client->getResponse()->getStatusCode());
//    }
//    public function testFormIsInvalidIfDescriptionHaveTooBigLength(): void
//    {
//        $client = static::createClient();
//
//        $client->loginUser($this->getUser());
//        $client->request('GET', '/wishes/create');
//
//        $description = str_repeat('x', 5001);
//
//        $client->submitForm("Create", [
//            'wish[title]' => 'Test',
//            'wish[description]' => $description,
//            'wish[category]' => '10'
//        ]);
//
//        $this->assertEquals(422, $client->getResponse()->getStatusCode());
//    }

    private function getUser(): Participant
    {
        $participantsRepository = static::getContainer()->get(ParticipantsRepository::class);
        return $participantsRepository->findOneBy(['pseudo' => 'admin']);
    }
}
