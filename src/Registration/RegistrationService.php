<?php

declare(strict_types=1);

namespace App\Registration;

use App\Shared\Entity\User;
use App\Shared\Exception\DuplicateUserException;
use App\Shared\Repository\UserRepository;
use App\Shared\Service\SlugCreator;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationService
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private SlugCreator $slugger,
    ) {
    }

    /**@throws DuplicateUserException */
    public function register(RegistrationDTO $registrationDTO): User
    {
        $userSlug = $this->slugger->createSlug($registrationDTO->username);

        $user = new User($registrationDTO->email, $registrationDTO->username, $userSlug);
        $user->setHashedPassword($this->passwordHasher->hashPassword($user, $registrationDTO->plainPassword));

        try {
            $this->userRepository->save($user, true);
        } catch (UniqueConstraintViolationException $previous) {
            throw new DuplicateUserException(
                'User with such email or username already exists',
                previous: $previous
            );
        }

        return $user;
    }
}
