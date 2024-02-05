<?php

declare(strict_types=1);

namespace App\Shared\Entity;

use App\Shared\Enum\Muscle;
use App\Shared\Repository\ExerciseMuscleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExerciseMuscleRepository::class)]
#[ORM\UniqueConstraint(fields: ['exercise', 'muscle'])] // unique muscle for every exercise, search by exercise
#[ORM\Index(fields: ['muscle'])] // search by muscle
class ExerciseMuscle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'targetMuscles')]
    #[ORM\JoinColumn(nullable: false)]
    private Exercise $exercise;

    #[ORM\Column(type: Types::SMALLINT, enumType: Muscle::class)]
    private Muscle $muscle;

    public function __construct(Exercise $exercise, Muscle $muscle)
    {
        $this->exercise = $exercise;
        $this->muscle = $muscle;
    }

    public function getMuscle(): Muscle
    {
        return $this->muscle;
    }
}
