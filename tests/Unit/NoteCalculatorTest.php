<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Model\Entity\NumberOfRatingPerValue;
use App\Model\Entity\Review;
use App\Model\Entity\VideoGame;
use App\Rating\CalculateAverageRating;
use App\Rating\CountRatingsPerValue;
use App\Rating\RatingHandler;
use App\Tests\Functional\FunctionalTestCase;
use Faker\Generator;
use Monolog\Test\TestCase;
use Random\RandomException;

final class NoteCalculatorTest extends TestCase
{
    /**
     * @throws RandomException
     */
    public function testNumberOfRatingIsTrue(): void
    {
        $videoGame = new VideoGame();

        $incrementation = random_int(1,100);

        for ($i = 0; $i < $incrementation; ++$i) {
            $review = new Review();
            $review->setRating(random_int(1,5));
            $review->setVideoGame($videoGame);
            dump($review->getRating());
        }

        dump($videoGame->getReviews());
//        dump($videoGame);
        $ratingHandler = new RatingHandler();
        $ratingHandler->countRatingsPerValue($videoGame);

        $expectedNumberOfRatingPerValue = $this->calculateExpectedRatings($videoGame);

        self::assertEquals($expectedNumberOfRatingPerValue, $videoGame->getNumberOfRatingsPerValue());
    }

//    private function calculateExpectedRatings(VideoGame $videoGame): array
//    {
//        dump($videoGame->getNumberOfRatingsPerValue());
//        $ratings = $videoGame->getNumberOfRatingsPerValue();
//        $ratingCount = array_fill(1, 5, 0); // Initialise un tableau pour compter de 1 à 5
//
//        foreach ($ratings as $rating) {
//            if (isset($ratingCount[$rating])) {
//                ++$ratingCount[$rating];
//            }
//        }
//
//        return $ratingCount;
//    }
}
