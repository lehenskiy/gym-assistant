<?php

declare(strict_types=1);

namespace App\UserProfile\Edit;

use App\Shared\Entity\User;
use App\Shared\Exception\DuplicateUserException;
use App\Shared\Repository\UserRepository;
use App\UserProfile\Edit\Exception\CurrentPasswordNotValidException;
use App\UserProfile\Edit\Exception\NewPasswordSameAsCurrentException;
use App\UserProfile\Edit\Exception\NewWeightSameAsPreviousException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EditProfileService
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    /**@throws DuplicateUserException
     * @throws CurrentPasswordNotValidException
     * @throws NewPasswordSameAsCurrentException
     * @throws NewWeightSameAsPreviousException
     */
    public function editUser(User $userToEdit, EditProfileDTO $editProfileDTO): User
    {
        $this->updateUserPassword($userToEdit, $editProfileDTO->currentPassword, $editProfileDTO->newPassword);
        if ($editProfileDTO->email !== null) {
            $userToEdit->changeEmail($editProfileDTO->email);
        }

        $userToEdit->updatePersonalInfo(
            $editProfileDTO->public,
            $editProfileDTO->gender,
            $editProfileDTO->birthDate,
            $editProfileDTO->height,
            $editProfileDTO->goalWeight,
        );

        if ($editProfileDTO->currentWeight !== null) {
            $this->updateUserCurrentWeight($userToEdit, $editProfileDTO->currentWeight);
        }

        $this->saveUserChanges($userToEdit);

        return $userToEdit;
    }

    private function updateUserPassword(User $userToEdit, ?string $currentPassword, ?string $newPassword): void
    {
        if ($currentPassword !== null && $newPassword !== null) {
            if (!$this->passwordHasher->isPasswordValid($userToEdit, $currentPassword)) {
                throw new CurrentPasswordNotValidException('Current password is not correct');
            }

            if ($userToEdit->getPassword() === $newPassword) {
                throw new NewPasswordSameAsCurrentException('New password should not be the same as current');
            }

            $hashedPassword = $this->passwordHasher->hashPassword($userToEdit, $newPassword);
            $userToEdit->setHashedPassword($hashedPassword);
        }
    }

    private function updateUserCurrentWeight(User $userToEdit, int $currentWeight): void
    {
        if (
            $userToEdit->getWeights()->first() !== false
            && $currentWeight === $userToEdit->getWeights()->first()->getWeight()
        ) {
            throw new NewWeightSameAsPreviousException('Current weight is the same as your previous weight');
        }
        $userToEdit->updateCurrentWeight($currentWeight);
    }

    private function saveUserChanges(User $user): void
    {
        try {
            $this->userRepository->save($user, true);
        } catch (UniqueConstraintViolationException $previous) {
            throw new DuplicateUserException(
                'User with such email already exists',
                previous: $previous
            );
        }
    }
}
