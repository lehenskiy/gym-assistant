<?php

declare(strict_types=1);

namespace App\Tests\integration\UserProfile\Edit;

use App\Shared\Entity\User;
use App\Shared\Entity\UserWeight;
use App\Shared\Exception\DuplicateUserException;
use App\Tests\Shared\Fixture\UserFixture;
use App\UserProfile\Edit\EditProfileDTO;
use App\UserProfile\Edit\EditProfileService;
use App\UserProfile\Edit\Exception\NewWeightSameAsPreviousException;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EditProfileSavingTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private User $anotherUserInDatabase;
    private User $userToEdit;
    private EditProfileService $editProfileService;

    private const USER_WEIGHT_NOT_SAVED = 'User weight not saved on edit profile';

    public function testEditProfileThrowsDuplicateUserException(): void
    {
        $editDataWithExistedUserEmail = new EditProfileDTO([
            'email' => $this->anotherUserInDatabase->getEmail(),
            'public' => false,
            'gender' => null,
            'birthdate' => null,
            'height' => null,
            'currentWeight' => null,
            'goalWeight' => null,
        ]);

        $this->expectException(DuplicateUserException::class);
        $this->editProfileService->editUser($this->userToEdit, $editDataWithExistedUserEmail);
    }

    public function testEditProfileSavesUserWeightAndThrowsSameWeightException(): void
    {
        $editDataWithCurrentWeight = new EditProfileDTO([
            'email' => null,
            'public' => false,
            'gender' => null,
            'birthdate' => null,
            'height' => null,
            'currentWeight' => 500,
            'goalWeight' => null,
        ]);
        $this->editProfileService->editUser($this->userToEdit, $editDataWithCurrentWeight);
        $userWeightRepository = $this->entityManager->getRepository(UserWeight::class);
        $expectedSavedUserWeight = $userWeightRepository->findOneBy([
            'weight' => $editDataWithCurrentWeight->currentWeight,
            'user' => $this->userToEdit
        ]);

        $this->assertInstanceOf(UserWeight::class, $expectedSavedUserWeight, self::USER_WEIGHT_NOT_SAVED);

        $this->expectException(NewWeightSameAsPreviousException::class);
        $this->editProfileService->editUser($this->userToEdit, $editDataWithCurrentWeight);
    }

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $container = self::bootKernel()->getContainer();

        $this->entityManager = $container->get('doctrine')->getManager();
        $this->editProfileService = $container->get(EditProfileService::class);

        $fixturesLoader = new ORMExecutor($this->entityManager, new ORMPurger());
        $fixturesLoader->execute([$userFixture = new UserFixture()]);
        $this->userToEdit = $userFixture->getReference(UserFixture::REFERENCED_USER_NAME, User::class);

        $fixturesLoader->execute([$anotherUserFixture = new UserFixture()], true);
        $this->anotherUserInDatabase = $anotherUserFixture->getReference(
            UserFixture::REFERENCED_USER_NAME,
            User::class
        );
    }
}
