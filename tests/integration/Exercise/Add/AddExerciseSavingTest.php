<?php

declare(strict_types=1);

namespace App\Tests\integration\Exercise\Add;

use App\Exercise\Add\AddExerciseDTO;
use App\Exercise\Add\AddExerciseService;
use App\Exercise\Add\DuplicateUserExerciseTitleException;
use App\Shared\Entity\Exercise;
use App\Shared\Entity\ExerciseMuscle;
use App\Shared\Entity\User;
use App\Tests\Shared\Fixture\UserFixture;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AddExerciseSavingTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private int $userAddingExerciseId;
    private AddExerciseService $addExerciseService;

    private const EXERCISE_NOT_SAVED = 'Exercise not saved in database, check if flushed';
    private const EXERCISE_TARGET_MUSCLES_NOT_SAVED = 'Exercise target muscles not saved in database, check if flushed';

    #[DataProviderExternal(AddExerciseProvider::class, 'addExerciseValidProvider')]
    public function testAddExerciseSavesExerciseAndTargetMuscles(AddExerciseDTO $givenDTO): void
    {
        $expectedSavedExercise = $this->addExerciseService->addExercise($givenDTO, $this->userAddingExerciseId);
        $this->entityManager->detach($expectedSavedExercise);
        // id not set if entity not saved, title is required for Exercise
        $exerciseRepository = $this->entityManager->getRepository(Exercise::class);
        $exerciseFromDatabase = $exerciseRepository->findOneBy(['title' => $expectedSavedExercise->getTitle()]);

        $this->assertInstanceOf(Exercise::class, $exerciseFromDatabase, self::EXERCISE_NOT_SAVED);

        $targetMusclesRepository = $this->entityManager->getRepository(ExerciseMuscle::class);
        $exerciseMusclesFromDatabase = $targetMusclesRepository->findBy(['exercise' => $exerciseFromDatabase]);
        $targetMusclesFromDatabase = [];
        foreach ($exerciseMusclesFromDatabase as $exerciseMuscle) {
            $targetMusclesFromDatabase[] = $exerciseMuscle->getMuscle();
        }
        $inputTargetMuscles = $givenDTO->targetMuscles ?? [];

        $this->assertEqualsCanonicalizing(
            $inputTargetMuscles,
            $targetMusclesFromDatabase,
            self::EXERCISE_TARGET_MUSCLES_NOT_SAVED
        );
    }

    #[DataProviderExternal(AddExerciseProvider::class, 'addExerciseValidProvider')]
    public function testAddExerciseServiceThrowsDuplicateException(AddExerciseDTO $givenDTO): void
    {
        $imageToCreate = $givenDTO->image?->getRealPath();
        $this->addExerciseService->addExercise($givenDTO, $this->userAddingExerciseId);
        if ($givenDTO->image !== null) {
            touch($imageToCreate);
        }
        $this->expectException(DuplicateUserExerciseTitleException::class);
        $this->addExerciseService->addExercise($givenDTO, $this->userAddingExerciseId);
    }

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $container = self::bootKernel()->getContainer();

        $this->entityManager = $container->get('doctrine')->getManager();
        $this->addExerciseService = $container->get(AddExerciseService::class);

        $fixturesLoader = new ORMExecutor($this->entityManager, new ORMPurger());
        $fixturesLoader->execute([$userFixture = new UserFixture()]);

        $this->userAddingExerciseId = $userFixture->getReference(UserFixture::REFERENCED_USER_NAME, User::class)
            ->getId();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // after any exception thrown during $em->flush() EntityManager closes, reset to continue testing
        if (!$this->entityManager->isOpen()) {
            $this->entityManager = self::getContainer()->get('doctrine')->resetManager();
        }
    }
}
