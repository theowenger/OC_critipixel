<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ShowTest extends FunctionalTestCase
{
    public function testShouldShowVideoGame(): void
    {
        $this->get('/jeu-video-0');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Jeu vidéo 0');
    }

    public function testShouldPostReview(): void
    {
        $this->login();
        $this->get('/jeu-video-0');
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        //Verifier que le lien de deconnexion existe
        self::assertSelectorExists('a.nav-link[href="/auth/logout"]', 'Le lien de déconnexion devrait être présent');

        // Vérifier que l'onglet "Avis" existe
        self::assertSelectorExists('a#tab-reviews', 'L\'onglet "Avis" devrait être présent');

        // Clic sur l'onglet "Avis"
        $link = $this->client->getCrawler()->filter('a.nav-link:contains("Avis")')->link();
        $this->client->click($link);

        self::assertSelectorExists('a#tab-reviews', 'L\'onglet "Avis" devrait être actif après le clic.');
//        self::assertSelectorExists('form', 'Le formulaire d\'avis devrait être visible.');


        $this->client->submitForm(
            "Poster",
            [
                'review[rating]' => 5,
                'review[comment]' => "lorem ipsum dolor sit amet",
            ]
        );
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
        self::assertSelectorTextContains('div.list-group-item:last-child h3', 'user+0');
        self::assertSelectorTextContains('div.list-group-item:last-child p', 'lorem ipsum dolor sit amet');
        self::assertSelectorTextContains('div.list-group-item:last-child span.value', '5');
    }
}
