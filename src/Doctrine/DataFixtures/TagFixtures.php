<?php

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;
use function array_fill_callback;

final class TagFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['default'];
    }
    public function load(ObjectManager $manager): void
    {
        $tags = array_fill_callback(
            0,
            25,
            static fn (int $index): Tag => (new Tag)->setName(sprintf('Tag %d', $index))
        );

        array_walk($tags, [$manager, 'persist']);

        $manager->flush();
    }
}