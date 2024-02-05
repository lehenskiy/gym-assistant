<?php

declare(strict_types=1);

namespace App\UserProfile\Show\DTO;

use App\Shared\Entity\Exercise;

readonly class CreatedExerciseDTO
{
    public string $title;
    public string $slug;
    public int $viewsNumber;

    public function __construct(array $exerciseData)
    {
        [
            'title' => $this->title,
            'slug' => $this->slug,
            'viewsNumber' => $this->viewsNumber,
        ] = $exerciseData;
    }

    public static function fromEntity(Exercise $exercise): self
    {
        $exerciseData = [
            'title' => $exercise->getTitle(),
            'slug' => $exercise->getSlug(),
            'viewsNumber' => $exercise->getViewsNumber(),
        ];

        return new self($exerciseData);
    }
}
