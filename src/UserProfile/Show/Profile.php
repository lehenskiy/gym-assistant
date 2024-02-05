<?php

declare(strict_types=1);

namespace App\UserProfile\Show;

use App\UserProfile\Show\DTO\PrivateProfileDTO;
use App\UserProfile\Show\DTO\PublicProfileDTO;

readonly class Profile
{
    public function __construct(public PublicProfileDTO $publicData, public ?PrivateProfileDTO $privateData)
    {
    }
}
