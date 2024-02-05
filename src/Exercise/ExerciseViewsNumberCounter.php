<?php

declare(strict_types=1);

namespace App\Exercise;

use App\Shared\Entity\Exercise;
use App\Shared\Repository\ExerciseRepository;

class ExerciseViewsNumberCounter
{
    public function __construct(private ExerciseRepository $exerciseRepository)
    {
    }

    public function incrementExerciseViewsNumber(Exercise $exercise): void
    {
        $exercise->updateViewsNumber($exercise->getViewsNumber() + 1);
        $this->exerciseRepository->save($exercise, true);
    }
}
