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
        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail('user+'.$i.'@email.com')
                ->setPlainPassword('password')
                ->setUsername('user+' . $i);

            $users[] = $user;
        }

        foreach ($users as $user) {
            $manager->persist($user);
        }

        $manager->flush();
    }
    }