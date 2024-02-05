<?php

declare(strict_types=1);

namespace App\Exercise\Show;

use App\Exercise\ExerciseTargetMusclesNamesGetter;
use App\Shared\Entity\Exercise;
use DateTimeImmutable;

readonly class ShowExerciseDTO
{
    public string $title;
    public string $authorUsername;
    public string $authorSlug;
    public ?string $description;
    public ?string $executionTechnique;
    public ?string $executionTips;
    public ?int $difficulty;
    public int $viewsNumber;
    public array $targetMuscles;
    public DateTimeImmutable $additionDate;
    public ?string $imageFilename;

    public function __construct(array $exerciseData)
    {
        [
            'title' => $this->title,
            'authorUsername' => $this->authorUsername,
            'authorSlug' => $this->authorSlug,
            'description' => $this->description,
            'executionTechnique' => $this->executionTechnique,
            'executionTips' => $this->executionTips,
            'difficulty' => $this->difficulty,
            'viewsNumber' => $this->viewsNumber,
            'additionDate' => $this->additionDate,
            'targetMusclesNames' => $this->targetMuscles,
            'imageFilename' => $this->imageFilename,
        ] = $exerciseData;
    }

    public static function fromEntity(Exercise $exercise): self
    {
        $exerciseData = [
            'title' => $exercise->getTitle(),
            'authorUsername' => $exercise->getAuthor()->getUsername(),
            'authorSlug' => $exercise->getAuthor()->getSlug(),
            'description' => $exercise->getDescription(),
            'executionTechnique' => $exercise->getExecutionTechnique(),
            'executionTips' => $exercise->getExecutionTips(),
            'difficulty' => $exercise->getDifficulty(),
            'viewsNumber' => $exercise->getViewsNumber(),
            'additionDate' => $exercise->getCreatedAt(),
            'targetMusclesNames' => ExerciseTargetMusclesNamesGetter::getTargetMusclesNamesForExercise($exercise),
            'imageFilename' => $exercise->getImageFilename(),
        ];

        return new self($exerciseData);
    }
}
