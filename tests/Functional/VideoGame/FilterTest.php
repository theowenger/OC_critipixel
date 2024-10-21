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
        $randomTag = random_int(0, 24);
        $tagId = $this->getTagIdByName('Tag ' . $randomTag);

        $params = [
            'page' => 1,
            'limit' => 10,
            'sorting' => 'ReleaseDate',
            'direction' => 'Descending',
            'filter' => [
                'tags' => [$tagId],
            ],
        ];

        // Exécuter la requête avec les paramètres
        $this->get('/?' . http_build_query($params));
        self::assertResponseIsSuccessful();
        $content = $this->client->getResponse()->getContent();

        $this->assertAllTagsContain($content, 'Tag ' . $randomTag);
    }

    private function assertAllTagsContain(string $content, string $expectedTag): void
    {
        preg_match_all('/<span class="tag">(.*?)<\/span>/', $content, $matches);

        foreach ($matches[1] as $tagContent) {
            $this->assertEquals($expectedTag, trim($tagContent), 'Le contenu du tag doit être "' . $expectedTag . '"');
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
