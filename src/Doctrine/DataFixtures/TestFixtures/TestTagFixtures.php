<?php

namespace App\Doctrine\DataFixtures\TestFixtures;

use App\Model\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;
use function array_fill_callback;

final class TestTagFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['test'];
    }

    public function load(ObjectManager $manager): void
    {
        $tags = [];
        for ($i = 0; $i < 25; $i++) {
            $tags[] = (new Tag())->setName(sprintf('Tag %d', $i));
        }

        foreach ($tags as $tag) {
            $manager->persist($tag);
        }

        $manager->flush();
    }
}