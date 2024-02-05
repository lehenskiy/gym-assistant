<?php

namespace App\Tests\Shared\Fixture;

use App\Shared\Entity\User;
use App\Shared\Enum\UserGender;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UserFixture extends Fixture
{
    public const REFERENCED_USER_NAME = 'user-in-database';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $userInDatabase = new User(
            $faker->email(),
            substr($faker->userName(), 0, 20),
            substr($faker->slug(2), 0, 20)
        );
        $userInDatabase
            ->setHashedPassword($faker->sha256())
            ->updatePersonalInfo(
                $faker->boolean(),
                $faker->randomElement(UserGender::cases()),
                DateTimeImmutable::createFromMutable($faker->dateTime()),
                $faker->numberBetween(100, 250),
                $faker->numberBetween(300, 2000),
            );

        $manager->persist($userInDatabase);
        $manager->flush();

        $this->setReference(self::REFERENCED_USER_NAME, $userInDatabase);
    }
}
