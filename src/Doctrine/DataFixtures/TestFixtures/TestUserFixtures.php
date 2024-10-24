<?php

namespace App\Doctrine\DataFixtures\TestFixtures;

use App\Model\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use function array_fill_callback;

final class TestUserFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['test'];
    }
    public function load(ObjectManager $manager): void
    {
        $users = array_fill_callback(0, 10, fn (int $index): User => (new User)
            ->setEmail(sprintf('user+%d@email.com', $index))
            ->setPlainPassword('password')
            ->setUsername(sprintf('user+%d', $index))
        );

        array_walk($users, [$manager, 'persist']);

        $manager->flush();
    }
}
