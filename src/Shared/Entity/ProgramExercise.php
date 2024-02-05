<?php

declare(strict_types=1);

namespace App\Shared\Entity;

use App\Shared\Repository\ProgramExerciseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProgramExerciseRepository::class)]
// possible to add same exercise on different positions
#[ORM\UniqueConstraint(columns: ['program_id', 'exercise_id', 'position'])]
class ProgramExercise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'programExercises')]
    #[ORM\JoinColumn(nullable: false)]
    private Program $program;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Exercise $exercise;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $position;

    public function __construct(Program $program, Exercise $exercise, int $position)
    {
        $this->program = $program;
        $this->exercise = $exercise;
        $this->position = $position;
    }

    public function getExercise(): Exercise
    {
        return $this->exercise;
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}
