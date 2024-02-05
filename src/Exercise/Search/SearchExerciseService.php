<?php

declare(strict_types=1);

namespace App\Exercise\Search;

use App\Shared\Repository\ExerciseRepository;

class SearchExerciseService
{
    public function __construct(private ExerciseRepository $exerciseRepository)
    {
    }

    public function searchForExercises(SearchExerciseParamsDTO $searchParamsDTO): array
    {
        return
            $this->exerciseRepository->getBySearchParams(
                $searchParamsDTO->titleToSearch,
                $searchParamsDTO->authorToSearch,
                $searchParamsDTO->difficultiesForFilter,
                $searchParamsDTO->containsImage,
                $searchParamsDTO->targetMusclesForFilter,
            );
    }
}
