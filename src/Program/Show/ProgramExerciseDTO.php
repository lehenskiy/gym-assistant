<?php

declare(strict_types=1);

namespace App\Program\Show;

use App\Shared\Entity\ProgramExercise;

readonly class ProgramExerciseDTO
{
    public string $title;
    public int $position;
    public string $slug;

    public function __construct(array $programExerciseData)
    {
        [
            'title' => $this->title,
            'position' => $this->position,
            'slug' => $this->slug,
        ] = $programExerciseData;
    }

    public static function fromEntity(ProgramExercise $programExercise): self
    {
        $exerciseData = [
            'title' => $programExercise->getExercise()->getTitle(),
            'position' => $programExercise->getPosition(),
            'slug' => $programExercise->getExercise()->getSlug(),
        ];

        return new self($exerciseData);
    }
}
