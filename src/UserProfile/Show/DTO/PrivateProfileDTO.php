<?php

declare(strict_types=1);

namespace App\UserProfile\Show\DTO;

use App\Shared\Enum\UserGender;
use DateTimeImmutable;

readonly class PrivateProfileDTO
{
    public string $email;
    public ?UserGender $gender;
    public ?DateTimeImmutable $birthDate;
    public ?int $height;
    public ?int $currentWeight;
    /**@var array<UserWeightDTO> */
    public array $weights;
    public ?int $goalWeight;
    public ?int $age;
    public ?int $bodyMassIndex;
    public ?string $bodyMassIndexInterpretation;
    public ?int $weightGained;
    public ?int $goalPercentage;

    public function __construct(array $userData)
    {
        [
            'email' => $this->email,
            'gender' => $this->gender,
            'birthdate' => $this->birthDate,
            'height' => $this->height,
            'currentWeight' => $this->currentWeight,
            'weightDTOs' => $this->weights,
            'goalWeight' => $this->goalWeight,
            'age' => $this->age,
            'bodyMassIndex' => $this->bodyMassIndex,
            'bodyMassIndexInterpretation' => $this->bodyMassIndexInterpretation,
            'weightGained' => $this->weightGained,
            'goalPercentage' => $this->goalPercentage,
        ] = $userData;
    }
}
