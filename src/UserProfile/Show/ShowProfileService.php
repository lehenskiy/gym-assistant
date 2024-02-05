<?php

declare(strict_types=1);

namespace App\UserProfile\Show;

use App\Shared\Entity\User;
use App\Shared\Repository\ExerciseRepository;
use App\UserProfile\Show\DTO\CreatedExerciseDTO;
use App\UserProfile\Show\DTO\PrivateProfileDTO;
use App\UserProfile\Show\DTO\PublicProfileDTO;
use App\UserProfile\Show\DTO\UserWeightDTO;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;

class ShowProfileService
{
    private const WEIGHT_MULTIPLIER = 10;

    public function __construct(private ExerciseRepository $exerciseRepository)
    {
    }

    public function getProfile(User $user, bool $isCurrentUserPage): Profile
    {
        if ($isCurrentUserPage || $user->getPublic()) {
            return $this->getFullProfile($user);
        }

        return $this->getProfileWithPublicInfoOnly($user);
    }

    private function getFullProfile(User $user): Profile
    {
        $publicProfile = $this->getPublicProfile($user);
        $privateProfile = $this->getPrivateProfile($user);

        return new Profile($publicProfile, $privateProfile);
    }

    private function getProfileWithPublicInfoOnly(User $user): Profile
    {
        return new Profile($this->getPublicProfile($user), null);
    }

    private function getPublicProfile(User $user): PublicProfileDTO
    {
        $publicProfileData = [
            'username' => $user->getUsername(),
            'registrationDate' => $user->getCreatedAt(),
            'showPersonalData' => $user->getPublic(),
            'daysAtSite' => $this->calculateDaysAtSite($user->getCreatedAt()),
            'mostPopularCreatedExerciseDTOs' => $this->getFiveMostPopularUserCreatedExerciseDTOs($user),
        ];

        return new PublicProfileDTO($publicProfileData);
    }

    private function getPrivateProfile(User $user): PrivateProfileDTO
    {
        $currentWeight = $user->getWeights()->first() === false ? null : $user->getWeights()->first()->getWeight();
        $startWeight = $user->getWeights()->last() === false ? null : $user->getWeights()->last()->getWeight();

        $privateProfileData = [
            'email' => $user->getEmail(),
            'gender' => $user->getGender(),
            'birthdate' => $user->getBirthDate(),
            'height' => $user->getHeight(),
            'currentWeight' => $currentWeight,
            'weightDTOs' => $this->getUserWeightDTOs($user),
            'goalWeight' => $user->getGoalWeight(),
            'age' => $this->calculateAge($user->getBirthDate()),
            'bodyMassIndex' => $bodyMassIndex = $this->calculateBodyMassIndex(
                $user->getHeight(),
                $currentWeight
            ),
            'bodyMassIndexInterpretation' => $this->interpreteBodyMassIndex($bodyMassIndex),
            'weightGained' => $weightGained = $this->calculateWeightGained($user->getWeights()),
            'goalPercentage' => $this->calculateGoalPercentage(
                $weightGained,
                $startWeight,
                $user->getGoalWeight()
            ),
        ];

        return new PrivateProfileDTO($privateProfileData);
    }

    private function getFiveMostPopularUserCreatedExerciseDTOs(User $user): array
    {
        $createdExercises = [];

        foreach ($this->exerciseRepository->getFiveMostPopularExercises($user) as $createdExercise) {
            $createdExercises[] = CreatedExerciseDTO::fromEntity($createdExercise);
        }

        return $createdExercises;
    }

    private function calculateDaysAtSite(DateTimeImmutable $registrationDate): int
    {
        $today = new DateTimeImmutable('now');

        return $registrationDate->diff($today)->days;
    }

    private function getUserWeightDTOs(User $user): array
    {
        $weightDTO = [];

        foreach ($user->getWeights() as $weight) {
            $weightDTO[] = UserWeightDTO::fromEntity($weight);
        }

        return $weightDTO;
    }

    private function calculateAge(?DateTimeImmutable $birthDate): ?int
    {
        if ($birthDate !== null) {
            $today = new DateTimeImmutable('now');

            return $birthDate->diff($today)->y;
        }

        return null;
    }

    private function calculateBodyMassIndex(?int $height, ?int $weight): ?int
    {
        if ($height !== null && $weight !== null) {
            return (int)(($weight * (self::WEIGHT_MULTIPLIER ** 5)) / ($height ** 2));
        }

        return null;
    }

    private function interpreteBodyMassIndex(?int $bodyMassIndex): ?string
    {
        return match (true) {
            $bodyMassIndex === null => null,
            $bodyMassIndex <= 1600 => 'Severe Thinness',
            $bodyMassIndex <= 1850 => 'Thinness',
            $bodyMassIndex <= 2500 => 'Normal',
            $bodyMassIndex <= 3000 => 'Overweight',
            $bodyMassIndex <= 3500 => 'Obese Class I',
            $bodyMassIndex <= 4000 => 'Obese Class II',
            $bodyMassIndex > 4000 => 'Obese Class III',
        };
    }

    /**@return ?int negative values if weight lost*/
    private function calculateWeightGained(Collection $weightsCollection): ?int
    {
        if (
            $weightsCollection->count() > 1
            && $weightsCollection->first() !== false
            && $weightsCollection->last() !== false
        ) {
            $currentWeight = $weightsCollection->first()->getWeight();
            $startWeight = $weightsCollection->last()->getWeight();
            $weightDiff = $currentWeight - $startWeight;

            return ($weightDiff === 0.0) ? null : $weightDiff;
        }

        return null;
    }

    private function calculateGoalPercentage(
        ?int $currentDiff,
        ?int $startWeight,
        ?int $goalWeight
    ): ?int {
        if ($currentDiff !== null && $goalWeight !== null) {
            return (int)(($currentDiff * 100 * self::WEIGHT_MULTIPLIER) / abs($startWeight - $goalWeight));
        }

        return null;
    }
}
