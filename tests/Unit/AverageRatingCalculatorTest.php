<?php

namespace App\Tests\Unit;

use App\Model\Entity\Review;
use App\Model\Entity\VideoGame;
use PHPUnit\Framework\TestCase;
use Random\RandomException;

class AverageRatingCalculatorTest extends TestCase
{

    /**
     * @throws RandomException
     */
    public function testThatAverageIsCorrectWithRating(): void
    {
        $videoGame = new VideoGame();

        $reviews = [];
        for ($i = 0; $i < 5; $i++) {
            $reviews[] = (new Review())->setRating(random_int(1, 5));
        }

        foreach ($reviews as $review) {
            $videoGame->getReviews()->add($review);
        }

        if (count($videoGame->getReviews()) === 0) {
            $videoGame->setAverageRating(null);
            return;
        }

        // Calcule la somme des notes
        $ratingsSum = array_sum(
            array_map(
                static fn(Review $review): int => $review->getRating(),
                $videoGame->getReviews()->toArray()
            )
        );

        // Calcule la moyenne et arrondit au plus proche entier
        $averageRating = round($ratingsSum / count($videoGame->getReviews()));
        $videoGame->setAverageRating((int) $averageRating);

        $expectedAverage = (int) round($ratingsSum / count($videoGame->getReviews()));

        $this->assertEquals($expectedAverage, $videoGame->getAverageRating(), "The average rating should be $expectedAverage");
    }
}
