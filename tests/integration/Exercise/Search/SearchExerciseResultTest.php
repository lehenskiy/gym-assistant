<?php

declare(strict_types=1);

namespace App\Tests\integration\Exercise\Search;

use App\Exercise\Search\SearchExerciseParamsDTO;
use App\Exercise\Search\SearchExerciseService;
use App\Shared\Entity\Exercise;
use App\Tests\Shared\Fixture\UserFixture;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SearchExerciseResultTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private SearchExerciseService $searchExerciseService;
    private Exercise $exerciseWithOnlyRequiredPropertiesDefined;
    private Exercise $exerciseWithAllPropertiesDefined;

    private const EXERCISES_NOT_INSERTED_INTO_DATABASE = 'No exercises found in the database';
    private const ALL_EXERCISES_SHOULD_BE_RETURNED_WITHOUT_SEARCH_PARAMS = 'All exercises should be returned if no search params provided';
    private const SEARCH_BY_ALL_PARAMS_NOT_RETURNS_EXPECTED_EXERCISE = 'Expected exercise with all properties defined to be returned';
    private const SEARCH_BY_ALL_PARAMS_RETURNS_NOT_EXPECTED_EXERCISE = 'Not expected exercise with only required properties defined to be returned';
    private const SEARCH_BY_TITLE_NOT_RETURNS_EXPECTED_EXERCISE = 'Expected exercise with defined title to be returned(should work with substr)';
    private const SEARCH_BY_AUTHOR_USERNAME_NOT_RETURNS_EXPECTED_EXERCISE = 'Expected exercise with defined author username to be returned(should work with substr)';
    private const SEARCH_BY_DIFFICULTY_NOT_RETURNS_EXPECTED_EXERCISE = 'Expected exercise with defined difficulty to be returned';
    private const SEARCH_BY_DIFFICULTY_RETURNS_NOT_EXPECTED_EXERCISE = 'Not expected exercise with not defined difficulty to be returned';
    private const SEARCH_BY_IMAGE_PRESENCE_NOT_RETURNS_EXPECTED_EXERCISE = 'Expected exercise with defined image filename to be returned';
    private const SEARCH_BY_IMAGE_PRESENCE_RETURNS_NOT_EXPECTED_EXERCISE = 'Not expected exercise with not defined image filename to be returned';
    private const SEARCH_BY_TARGET_MUSCLES_NOT_RETURNS_EXPECTED_EXERCISE = 'Expected exercise with defined target muscles to be returned';
    private const SEARCH_BY_TARGET_MUSCLES_RETURNS_NOT_EXPECTED_EXERCISE = 'Not expected exercise with not defined target muscles to be returned';

    public function testSearchWithoutParamsReturnsAllExercises(): void
    {
        $exerciseRepository = $this->entityManager->getRepository(Exercise::class);
        $allExercises = $exerciseRepository->findAll();
        $this->assertNotEmpty($allExercises, self::EXERCISES_NOT_INSERTED_INTO_DATABASE);

        $foundExercises = $this->searchExerciseService->searchForExercises(new SearchExerciseParamsDTO());

        $this->assertEqualsCanonicalizing(
            $allExercises,
            $foundExercises,
            self::ALL_EXERCISES_SHOULD_BE_RETURNED_WITHOUT_SEARCH_PARAMS
        );
    }

    /*
     * if all test methods with single search param work correctly and method with all search params too,
     * then searchService returns correct result.
     * Also, easier to locate mistake by single methods rather than combined one, since service supposed to change often
     */
    public function testSearchWithAllParamsFiltersExercises(): void
    {
        $searchParams = new SearchExerciseParamsDTO();
        $searchParams->titleToSearch = substr($this->exerciseWithAllPropertiesDefined->getTitle(), 1, -1);
        $searchParams->authorToSearch = substr(
            $this->exerciseWithAllPropertiesDefined->getAuthor()->getUsername(),
            1,
            -1
        );
        $searchParams->difficultiesForFilter = [$this->exerciseWithAllPropertiesDefined->getDifficulty()];
        $searchParams->containsImage = true;
        $searchParams->targetMusclesForFilter = [
            $this->exerciseWithAllPropertiesDefined->getTargetMuscles()->first()->getMuscle(),
            $this->exerciseWithAllPropertiesDefined->getTargetMuscles()->last()->getMuscle(),
        ];

        $searchResult = $this->searchExerciseService->searchForExercises($searchParams);
        $this->assertContains(
            $this->exerciseWithAllPropertiesDefined,
            $searchResult,
            self::SEARCH_BY_ALL_PARAMS_NOT_RETURNS_EXPECTED_EXERCISE
        );
        $this->assertNotContains(
            $this->exerciseWithOnlyRequiredPropertiesDefined,
            $searchResult,
            self::SEARCH_BY_ALL_PARAMS_RETURNS_NOT_EXPECTED_EXERCISE
        );
    }

    public function testSearchExerciseByTitleReturnsExpectedExercise(): void
    {
        $searchParams = new SearchExerciseParamsDTO();
        $searchParams->titleToSearch = substr($this->exerciseWithAllPropertiesDefined->getTitle(), 1, -1);

        $searchResult = $this->searchExerciseService->searchForExercises($searchParams);
        $this->assertContains(
            $this->exerciseWithAllPropertiesDefined,
            $searchResult,
            self::SEARCH_BY_TITLE_NOT_RETURNS_EXPECTED_EXERCISE
        );
    }

    public function testSearchExerciseByAuthorUsernameReturnsExpectedExercise(): void
    {
        $searchParams = new SearchExerciseParamsDTO();
        $searchParams->authorToSearch = substr(
            $this->exerciseWithAllPropertiesDefined->getAuthor()->getUsername(),
            1,
            -1
        );

        $searchResult = $this->searchExerciseService->searchForExercises($searchParams);
        $this->assertContains(
            $this->exerciseWithAllPropertiesDefined,
            $searchResult,
            self::SEARCH_BY_AUTHOR_USERNAME_NOT_RETURNS_EXPECTED_EXERCISE
        );
    }

    public function testSearchExerciseByDifficultyReturnsExpectedExercise(): void
    {
        $searchParams = new SearchExerciseParamsDTO();
        $searchParams->difficultiesForFilter = [$this->exerciseWithAllPropertiesDefined->getDifficulty()];

        $searchResult = $this->searchExerciseService->searchForExercises($searchParams);
        $this->assertContains(
            $this->exerciseWithAllPropertiesDefined,
            $searchResult,
            self::SEARCH_BY_DIFFICULTY_NOT_RETURNS_EXPECTED_EXERCISE
        );
        $this->assertNotContains(
            $this->exerciseWithOnlyRequiredPropertiesDefined,
            $searchResult,
            self::SEARCH_BY_DIFFICULTY_RETURNS_NOT_EXPECTED_EXERCISE
        );
    }

    public function testSearchExerciseByImagePresenceReturnsExpectedExercise(): void
    {
        $searchParams = new SearchExerciseParamsDTO();
        $searchParams->containsImage = true;

        $searchResult = $this->searchExerciseService->searchForExercises($searchParams);
        $this->assertContains(
            $this->exerciseWithAllPropertiesDefined,
            $searchResult,
            self::SEARCH_BY_IMAGE_PRESENCE_NOT_RETURNS_EXPECTED_EXERCISE
        );
        $this->assertNotContains(
            $this->exerciseWithOnlyRequiredPropertiesDefined,
            $searchResult,
            self::SEARCH_BY_IMAGE_PRESENCE_RETURNS_NOT_EXPECTED_EXERCISE
        );
    }

    public function testSearchExerciseByTargetMusclesReturnsExpectedExercise(): void
    {
        $searchParams = new SearchExerciseParamsDTO();
        $searchParams->targetMusclesForFilter = [
            $this->exerciseWithAllPropertiesDefined->getTargetMuscles()->first()->getMuscle(),
            $this->exerciseWithAllPropertiesDefined->getTargetMuscles()->last()->getMuscle(),
        ];

        $searchResult = $this->searchExerciseService->searchForExercises($searchParams);
        $this->assertContains(
            $this->exerciseWithAllPropertiesDefined,
            $searchResult,
            self::SEARCH_BY_TARGET_MUSCLES_NOT_RETURNS_EXPECTED_EXERCISE
        );
        $this->assertNotContains(
            $this->exerciseWithOnlyRequiredPropertiesDefined,
            $searchResult,
            self::SEARCH_BY_TARGET_MUSCLES_RETURNS_NOT_EXPECTED_EXERCISE
        );
    }

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $container = self::bootKernel()->getContainer();

        $this->entityManager = $container->get('doctrine')->getManager();
        $this->searchExerciseService = $container->get(SearchExerciseService::class);

        $fixturesLoader = new ORMExecutor($this->entityManager, new ORMPurger());
        $fixturesLoader->execute([new UserFixture(), $exercisesFixture = new SearchExerciseFixture()]);

        $this->exerciseWithOnlyRequiredPropertiesDefined = $exercisesFixture->getReference(
            SearchExerciseFixture::REFERENCED_EXERCISE_WITH_ONLY_REQUIRED_PROPERTIES_DEFINED,
            Exercise::class
        );
        $this->exerciseWithAllPropertiesDefined = $exercisesFixture->getReference(
            SearchExerciseFixture::REFERENCED_EXERCISE_WITH_ALL_PROPERTIES_DEFINED,
            Exercise::class
        );
    }
}
