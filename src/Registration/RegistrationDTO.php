<?php

declare(strict_types=1);

namespace App\Registration;

use Symfony\Component\Validator\Constraints as Assert;

class RegistrationDTO
{
    #[Assert\NotBlank(message: 'Please enter an email')]
    #[Assert\Email(message: 'Please enter a valid email')]
    public string $email;

    #[Assert\NotBlank(message: 'Please enter your username')]
    #[Assert\Length(
        min: 5,
        max: 20,
        minMessage: 'Username should be at least 5 symbols',
        maxMessage: 'Username should be no longer than 20 symbols',
    )]
    #[Assert\NoSuspiciousCharacters]
    #[Assert\Regex(
        pattern: '/^[A-Za-z0-9][A-Za-z0-9 _]*[A-Za-z0-9]$/',
        message: 'Username should contain only letters (English), numbers, underscores and whitespaces. It should not start or end with a underscore or whitespace.',
    )]
    public string $username;

    #[Assert\NotBlank(message: 'Please enter a password')]
    #[Assert\Length(min: 7, minMessage: 'Your password should be at least 7 characters')]
    public string $plainPassword;
}
