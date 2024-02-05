<?php

declare(strict_types=1);

namespace App\Exercise\Search;

use App\Shared\Enum\Muscle;
use Symfony\Component\Validator\Constraints as Assert;

class SearchExerciseParamsDTO
{
    #[Assert\NotBlank(allowNull: true)]
    public ?string $titleToSearch = null;

    #[Assert\NotBlank(allowNull: true)]
    public ?string $authorToSearch = null;

    #[Assert\AtLeastOneOf(
        constraints: [
            new Assert\Expression('value === [null]'),
            new Assert\Choice(choices: [1, 2, 3, 4, 5], multiple: true, max: 5),
        ],
    )]
    public ?array $difficultiesForFilter = null;

    public ?bool $containsImage = null;

    #[Assert\All(new Assert\Type(type: Muscle::class))]
    public ?array $targetMusclesForFilter = null;
}
