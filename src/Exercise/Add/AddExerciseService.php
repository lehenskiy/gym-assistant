<?php

declare(strict_types=1);

namespace App\Exercise\Add;

use App\Shared\Entity\Exercise;
use App\Shared\Repository\ExerciseRepository;
use App\Shared\Repository\UserRepository;
use App\Shared\Service\ImageUploader;
use App\Shared\Service\SlugCreator;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class AddExerciseService
{
    public function __construct(
        private ImageUploader $exerciseImageUploader,
        private SlugCreator $slugger,
        private ExerciseRepository $exerciseRepository,
        private UserRepository $userRepository,
    ) {
    }

    /**@throws DuplicateUserExerciseTitleException */
    public function addExercise(AddExerciseDTO $exerciseDTO, int $userAddingExerciseId): Exercise
    {
        $userAddingExercise = $this->userRepository->find($userAddingExerciseId);
        $exerciseSlug = $this->slugger->createSlug($userAddingExercise->getUsername(), $exerciseDTO->title);
        $exerciseImageFilename = $exerciseDTO->image === null
            ? null
            : $this->exerciseImageUploader->upload($exerciseDTO->image, $exerciseSlug);

        $addedExercise = (new Exercise($exerciseDTO->title, $userAddingExercise, $exerciseSlug))
            ->setAdditionalInfo(
                $exerciseDTO->description,
                $exerciseDTO->executionTechnique,
                $exerciseDTO->executionTips,
                $exerciseDTO->difficulty,
                $exerciseImageFilename,
                $exerciseDTO->targetMuscles ?? [],
            );

        $this->saveExercise($addedExercise);

        return $addedExercise;
    }

    private function saveExercise(Exercise $exercise): void
    {
        try {
            $this->exerciseRepository->save($exercise, true);
        } catch (UniqueConstraintViolationException $exception) {
            throw new DuplicateUserExerciseTitleException(
                'You have already created an exercise with this title, please rename this exercise or update the previous one',
                previous: $exception
            );
        }
    }
}
