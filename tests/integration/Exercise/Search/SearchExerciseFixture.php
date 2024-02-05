<?php

declare(strict_types=1);

namespace App\Tests\integration\Exercise\Search;

use App\Shared\Entity\Exercise;
use App\Shared\Entity\ExerciseMuscle;
use App\Shared\Entity\User;
use App\Shared\Enum\Muscle;
use App\Tests\Shared\Fixture\UserFixture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class SearchExerciseFixture extends Fixture
{
    private Generator $faker;

    public const REFERENCED_EXERCISE_WITH_ONLY_REQUIRED_PROPERTIES_DEFINED = 'exercise-min';
    public const REFERENCED_EXERCISE_WITH_ALL_PROPERTIES_DEFINED = 'exercise-max';

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        $randomExerciseAuthor = $this->getReference(UserFixture::REFERENCED_USER_NAME, User::class);

        $exerciseWithAllPropertiesDefined = new Exercise(
            $this->getRandomExerciseTitle(),
            $randomExerciseAuthor,
            $this->getRandomExerciseSlug(),
        );

        $exerciseWithAllPropertiesDefined->setAdditionalInfo(
            $this->faker->text(100),
            $this->faker->text(150),
            $this->faker->text(100),
            $this->faker->numberBetween(1, 5),
            $this->faker->text(100),
            $this->faker->randomElements(Muscle::cases(), 4), // not less than 2 for testing
        );
        $manager->persist($exerciseWithAllPropertiesDefined);

        $exerciseWithOnlyRequiredPropertiesDefined = new Exercise(
            $this->getRandomExerciseTitle(),
            $randomExerciseAuthor,
            $this->getRandomExerciseSlug(),
        );

        $manager->persist($exerciseWithOnlyRequiredPropertiesDefined);

        $manager->flush();

        $this->setReference(self::REFERENCED_EXERCISE_WITH_ONLY_REQUIRED_PROPERTIES_DEFINED, $exerciseWithOnlyRequiredPropertiesDefined);
        $this->setReference(self::REFERENCED_EXERCISE_WITH_ALL_PROPERTIES_DEFINED, $exerciseWithAllPropertiesDefined);
    }

    private function getRandomExerciseTitle(): string
    {
        return substr($this->faker->words(2, true), 0, 20);
    }

    private function getRandomExerciseSlug(): string
    {
        return substr($this->faker->slug(4), 0, 40);
    }
}
