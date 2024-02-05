<?php

declare(strict_types=1);

namespace App\Tests\integration\Exercise\Add;

use App\Exercise\Add\AddExerciseDTO;
use App\Shared\Enum\Muscle;
use Faker\Factory;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AddExerciseProvider
{
    public static function addExerciseValidProvider(): array
    {
        return [
            'Completely filled' => [self::getCompletelyFilledValidDTO()],
            'Minimum info' => [self::getMinimumValidDTO()],
        ];
    }

    private static function getCompletelyFilledValidDTO(): AddExerciseDTO
    {
        $faker = Factory::create();

        // created file in /tmp will be deleted until $file->move executed, so creating in existing permanent dir
        $imageFilename = $faker->word() . '.png';
        $imageToUpload = '/var/www/var/' . $imageFilename;
        touch($imageToUpload);
        $uploadedImage = new UploadedFile($imageToUpload, $imageFilename, 'image/png', test: true);

        $dto = new AddExerciseDTO();
        $dto->title = substr($faker->words(2, true), 0, 20);
        $dto->difficulty = $faker->numberBetween(1, 5);
        $dto->description = $faker->text(100);
        $dto->executionTechnique = $faker->text(150);
        $dto->executionTips = $faker->text(100);
        $dto->image = $uploadedImage;
        $dto->targetMuscles = $faker->randomElements(Muscle::cases(), $faker->numberBetween(1, count(Muscle::cases())));

        return $dto;
    }

    private static function getMinimumValidDTO(): AddExerciseDTO
    {
        $faker = Factory::create();

        $dto = new AddExerciseDTO();
        $dto->title = $faker->text(19);

        return $dto;
    }
}
