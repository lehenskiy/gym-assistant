<?php

declare(strict_types=1);

namespace App\UserProfile\Show\DTO;

use DateTimeImmutable;

readonly class PublicProfileDTO
{
    public string $username;
    public DateTimeImmutable $registrationDate;
    public bool $showPersonalData;
    public int $daysAtSite;

    /**@var array<CreatedExerciseDTO> */
    public array $mostPopularCreatedExercises;

    public function __construct(array $userData)
    {
        [
            'username' => $this->username,
            'registrationDate' => $this->registrationDate,
            'showPersonalData' => $this->showPersonalData,
            'daysAtSite' => $this->daysAtSite,
            'mostPopularCreatedExerciseDTOs' => $this->mostPopularCreatedExercises,
        ] = $userData;
    }
}
