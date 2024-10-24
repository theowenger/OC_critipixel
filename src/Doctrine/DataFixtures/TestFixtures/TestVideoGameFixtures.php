<?php

namespace App\Doctrine\DataFixtures\TestFixtures;

use App\Model\Entity\Review;
use App\Model\Entity\Tag;
use App\Model\Entity\User;
use App\Model\Entity\VideoGame;
use App\Rating\CalculateAverageRating;
use App\Rating\CountRatingsPerValue;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;
use function array_fill_callback;

final class TestVideoGameFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        private readonly Generator              $faker,
        private readonly CalculateAverageRating $calculateAverageRating,
        private readonly CountRatingsPerValue   $countRatingsPerValue
    ) {}

    public static function getGroups(): array
    {
        return ['test'];
    }

    public function load(ObjectManager $manager): void
    {
        $tags = $manager->getRepository(Tag::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();

        // Remplace array_fill_callback par une boucle for
        $videoGames = [];
        for ($index = 0; $index < 50; $index++) {
            $videoGame = (new VideoGame())
                ->setTitle(sprintf('Jeu vidéo %d', $index))
                ->setDescription($this->faker->paragraphs(10, true))
                ->setReleaseDate(new DateTimeImmutable())
                ->setTest($this->faker->paragraphs(6, true))
                ->setRating(($index % 5) + 1)
                ->setImageName(sprintf('video_game_%d.png', $index))
                ->setImageSize(2_098_872);

            // Ajout des tags en fonction du titre
            if ($videoGame->getTitle() === "Jeu vidéo 0") {
                $videoGame->getTags()->add($tags[0]);
                $videoGame->getTags()->add($tags[1]);
                $videoGame->getTags()->add($tags[2]);
            }

            if ($videoGame->getTitle() === "Jeu vidéo 1") {
                $videoGame->getTags()->add($tags[2]);
                $videoGame->getTags()->add($tags[3]);
                $videoGame->getTags()->add($tags[4]);
            }

            $videoGames[] = $videoGame;
        }

        // Remplace array_walk par une boucle foreach pour persister
        foreach ($videoGames as $videoGame) {
            $manager->persist($videoGame);
        }

        $manager->flush();

        // Ajout des reviews
        foreach ($videoGames as $videoGame) {
            shuffle($users);

            foreach (array_slice($users, 0, 5) as $user) {
                if ($user->getUsername() !== "user+0") {
                    $review = (new Review())
                        ->setUser($user)
                        ->setVideoGame($videoGame)
                        ->setRating($this->faker->numberBetween(1, 5))
                        ->setComment($this->faker->paragraphs(1, true));

                    $videoGame->getReviews()->add($review);
                    $manager->persist($review);

                    // Appels aux méthodes de calcul
                    $this->calculateAverageRating->calculateAverage($videoGame);
                    $this->countRatingsPerValue->countRatingsPerValue($videoGame);
                }
            }
        }

        $manager->flush();
    }

    /**
     * @return class-string[]
     */
    public function getDependencies(): array
    {
        return [TestTagFixtures::class, TestUserFixtures::class];
    }
}
