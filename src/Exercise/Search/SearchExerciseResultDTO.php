<?php

declare(strict_types=1);

namespace App\Exercise\Search;

use App\Exercise\ExerciseTargetMusclesNamesGetter;
use App\Shared\Entity\Exercise;

class SearchExerciseResultDTO
{
    public string $title;
    public string $authorUsername;
    public string $authorSlug;
    public string $slug;
    public ?int $difficulty;
    public bool $containsImage;
    public array $targetMuscles;
    public int $viewsNumber;

    public function __construct(array $exerciseData)
    {
        [
            'title' => $this->title,
            'authorUsername' => $this->authorUsername,
            'authorSlug' => $this->authorSlug,
            'slug' => $this->slug,
            'difficulty' => $this->difficulty,
            'containsImage' => $this->containsImage,
            'targetMusclesNames' => $this->targetMuscles,
            'viewsNumber' => $this->viewsNumber,
        ] = $exerciseData;
    }

    public static function fromEntity(Exercise $exercise): self
    {
        $exerciseData = [
            'title' => $exercise->getTitle(),
            'authorUsername' => $exercise->getAuthor()->getUsername(),
            'authorSlug' => $exercise->getAuthor()->getSlug(),
            'slug' => $exercise->getSlug(),
            'difficulty' => $exercise->getDifficulty(),
            'containsImage' => !($exercise->getImageFilename() === null),
            'targetMusclesNames' => ExerciseTargetMusclesNamesGetter::getTargetMusclesNamesForExercise($exercise),
            'viewsNumber' => $exercise->getViewsNumber(),
        ];

        return new self($exerciseData);
    }
}
