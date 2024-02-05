<?php

declare(strict_types=1);

namespace App\Program\Show;

use App\Shared\Entity\ExerciseMuscle;
use App\Shared\Entity\Program;
use DateTimeImmutable;

readonly class ShowProgramDTO
{
    public string $title;
    public ?string $description;
    public string $authorUsername;
    public string $authorSlug;

    /**@var array<ProgramExerciseDTO> */
    public array $exercises;
    public array $targetMuscles;
    public ?string $imageFilename;
    public DateTimeImmutable $additionDate;

    public function __construct(array $programData)
    {
        [
            'title' => $this->title,
            'description' => $this->description,
            'authorUsername' => $this->authorUsername,
            'authorSlug' => $this->authorSlug,
            'programExerciseDTOs' => $this->exercises,
            'targetMusclesNames' => $this->targetMuscles,
            'imageFilename' => $this->imageFilename,
            'additionDate' => $this->additionDate,
        ] = $programData;
    }

    public static function fromEntity(Program $program): self
    {
        $programData = [
            'title' => $program->getTitle(),
            'authorUsername' => $program->getAuthor()->getUsername(),
            'authorSlug' => $program->getAuthor()->getSlug(),
            'description' => $program->getDescription(),
            'programExerciseDTOs' => self::getProgramExerciseDTOsFromProgram($program),
            'targetMusclesNames' => self::getTargetMusclesNamesForProgram($program),
            'imageFilename' => $program->getImageFilename(),
            'additionDate' => $program->getCreatedAt(),
        ];

        return new self($programData);
    }

    /**@return array<ProgramExerciseDTO> */
    private static function getProgramExerciseDTOsFromProgram(Program $program): array
    {
        $exercises = [];

        foreach ($program->getProgramExercises() as $programExercise) {
            $exercises[] = ProgramExerciseDTO::fromEntity($programExercise);
        }

        return $exercises;
    }

    private static function getTargetMusclesNamesForProgram(Program $program): array
    {
        $targetMusclesNamesWithDuplicates = [];

        foreach ($program->getProgramExercises() as $programExercise) {
            /**@param $targetMuscle ExerciseMuscle */
            foreach ($programExercise->getExercise()->getTargetMuscles() as $targetMuscle) {
                $targetMusclesNamesWithDuplicates[] = $targetMuscle->getMuscle()->fullName();
            }
        }

        return array_unique($targetMusclesNamesWithDuplicates);
    }
}
