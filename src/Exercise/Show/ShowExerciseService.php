<?php

declare(strict_types=1);

namespace App\Exercise\Show;

use App\Shared\Entity\Exercise;
use App\Shared\Repository\ExerciseRepository;

class ShowExerciseService
{
    public function __construct(private ExerciseRepository $exerciseRepository)
    {
    }

    public function findExerciseBySlug(string $slug): ?Exercise
    {
        return $this->exerciseRepository->findBySlugWithAuthorAndTargetMuscles($slug);
    }
}
