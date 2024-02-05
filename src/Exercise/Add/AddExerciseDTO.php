<?php

declare(strict_types=1);

namespace App\Exercise\Add;

use App\Shared\Enum\Muscle;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class AddExerciseDTO
{
    #[Assert\Length(
        min: 5,
        max: 20,
        minMessage: 'Title should be at least 5 symbols',
        maxMessage: 'Title should be no longer than 20 symbols',
    )]
    #[Assert\NotNull(message: 'Title should be provided')]
    #[Assert\NoSuspiciousCharacters]
    #[Assert\Regex(
        pattern: '/^[\p{L}\p{N}\p{Zs}]+$/mu',
        message: 'Title should contain only letters, numbers and whitespaces',
    )]
    public string $title;

    #[Assert\NotBlank(message: 'Description cannot be a blank string', allowNull: true)]
    #[Assert\Length(max: 511, maxMessage: 'Description is too long')]
    public ?string $description = null;

    #[Assert\NotBlank(message: 'Execution technique cannot be a blank string', allowNull: true)]
    #[Assert\Length(max: 1023, maxMessage: 'Execution technique is too long')]
    public ?string $executionTechnique = null;

    #[Assert\NotBlank(message: 'Execution tips cannot be a blank string', allowNull: true)]
    #[Assert\Length(max: 511, maxMessage: 'Execution tips are too long')]
    public ?string $executionTips = null;

    #[Assert\Range(notInRangeMessage: 'Difficulty must be between 1 and 5', min: 1, max: 5)]
    public ?int $difficulty = null;

    #[Assert\All(new Assert\Type(type: Muscle::class))]
    #[Assert\Unique(message: 'Target muscles should be unique')]
    public ?array $targetMuscles = null;

    #[Assert\Image(
        mimeTypes: ['image/png', 'image/jpeg', 'image/gif'],
        minWidth: 300,
        maxWidth: 800,
        maxHeight: 600,
        minHeight: 200,
        mimeTypesMessage: 'Image should be in png, jpeg or gif format',
    )]
    public ?UploadedFile $image = null;
}
