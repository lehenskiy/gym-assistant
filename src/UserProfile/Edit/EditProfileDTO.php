<?php

declare(strict_types=1);

namespace App\UserProfile\Edit;

use App\Shared\Entity\User;
use App\Shared\Enum\UserGender;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

class EditProfileDTO
{
    #[Assert\Email(message: 'Please enter a valid email')]
    #[Assert\NotBlank(allowNull: false)]
    public ?string $email;

    #[Assert\Length(min: 7, minMessage: 'Your password should be at least 7 characters')]
    #[Assert\Expression(
        'value === null || (value !== null && this.newPassword !== null)',
        'Please enter your new password',
    )]
    public ?string $currentPassword;

    #[Assert\Length(min: 7, minMessage: 'Your new password should be at least 7 characters')]
    #[Assert\NotIdenticalTo(propertyPath: 'currentPassword', message: 'New password cannot be the same as previous')]
    #[Assert\Expression(
        'value === null || (value !== null && this.currentPassword !== null)',
        'Please enter your previous password',
    )]
    public ?string $newPassword;

    #[Assert\Type('bool')]
    public bool $public;

    #[Assert\Type(type: UserGender::class)]
    public ?UserGender $gender;

    #[Assert\LessThanOrEqual('-18 years', message: 'You must be at least 18 years old')]
    #[Assert\GreaterThanOrEqual('-100 years', message: 'You must be no more than 100 years old')]
    public ?DateTimeImmutable $birthDate;

    #[Assert\Range(notInRangeMessage: 'Enter correct height', min: 100, max: 250)]
    public ?int $height;

    #[Assert\Range(notInRangeMessage: 'Enter correct weight', min: 300, max: 2000)]
    public ?int $currentWeight;

    #[Assert\Range(notInRangeMessage: 'Enter correct weight', min: 300, max: 2000)]
    public ?int $goalWeight;

    public function __construct(array $userData)
    {
        [
            'email' => $this->email,
            'public' => $this->public,
            'gender' => $this->gender,
            'birthdate' => $this->birthDate,
            'height' => $this->height,
            'currentWeight' => $this->currentWeight,
            'goalWeight' => $this->goalWeight,
        ] = $userData;
        $this->currentPassword = $userData['currentPassword'] ?? null;
        $this->newPassword = $userData['newPassword'] ?? null;
    }

    public static function fromEntity(User $user): self
    {
        $userData = [
            'email' => $user->getEmail(),
            'public' => $user->getPublic(),
            'gender' => $user->getGender(),
            'birthdate' => $user->getBirthDate(),
            'height' => $user->getHeight(),
            'currentWeight' =>
                $user->getWeights()->first() === false
                    ? null
                    : $user->getWeights()->first()->getWeight(),
            'goalWeight' => $user->getGoalWeight(),
        ];

        return new EditProfileDTO($userData);
    }
}
