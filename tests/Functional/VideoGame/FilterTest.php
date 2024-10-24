<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Model\Entity\Tag;
use App\Tests\Functional\FunctionalTestCase;
use Random\RandomException;

final class FilterTest extends FunctionalTestCase
{
    public function testShouldListTenVideoGames(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');
        $this->client->clickLink('2');
        self::assertResponseIsSuccessful();
    }

    public function testShouldFilterVideoGamesBySearch(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');
        $this->client->submitForm('Filtrer', ['filter[search]' => 'Jeu vidéo 49'], 'GET');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(1, 'article.game-card');
    }

    /**
     * @throws RandomException
     */
    public function testShouldFilterVideoGamesTags(): void
    {
        // Cas de test avec les tags et les textes attendus
        $testCases = [
            [['Tag 0', 'Tag 1', 'Tag 2'], 'Affiche 1 jeux vidéo de 1 à 1 sur les 1 jeux vidéo'],
            [['Tag 2'], 'Affiche 2 jeux vidéo de 1 à 2 sur les 2 jeux vidéo'],
            [['Tag 5'], 'Affiche 0 jeux vidéo de 1 à 0 sur les 0 jeux vidéo'],
        ];

        foreach ($testCases as [$tags, $expectedText]) {
            // Récupérer les ID des tags
            $tagIds = array_map([$this, 'getTagIdByName'], $tags);

            $params = [
                'page' => 1,
                'limit' => 10,
                'sorting' => 'ReleaseDate',
                'direction' => 'Descending',
                'filter' => [
                    'tags' => $tagIds,
                ],
            ];

            // Exécuter la requête avec les paramètres
            $this->get('/?' . http_build_query($params));
            self::assertResponseIsSuccessful();

            // Vérification du texte affiché dans la div
            $crawler = $this->client->getCrawler();
            $divText = $crawler->filter('.fw-bold')->text();
            self::assertEquals($expectedText, trim($divText), 'Le texte dans la div .fw-bold doit correspondre à l\'attendu.');

            $videoGameNode = $crawler->filter('a.text-decoration-none');
            if ($videoGameNode->count() !== 0) {
                $videoGameLink = $videoGameNode->first()->text();
                self::assertEquals('Jeu vidéo 0', trim($videoGameLink), 'Le nom du jeu vidéo doit être "Jeu vidéo 0".');
            }
        }
    }

    private function getTagIdByName(string $tagName): ?int
    {
        $tag = $this->getEntityManager()
            ->getRepository(Tag::class)
            ->findOneBy(['name' => $tagName]);

        return $tag ? $tag->getId() : null;
    }
}
