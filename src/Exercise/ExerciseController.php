<?php

declare(strict_types=1);

namespace App\Exercise;

use App\Exercise\Add\AddExerciseForm;
use App\Exercise\Add\AddExerciseService;
use App\Exercise\Add\DuplicateUserExerciseTitleException;
use App\Exercise\Search\SearchExerciseResultDTO;
use App\Exercise\Search\SearchExerciseService;
use App\Exercise\Search\SearchForm;
use App\Exercise\Show\ShowExerciseDTO;
use App\Exercise\Show\ShowExerciseService;
use App\Shared\Exception\UnableToSaveFileException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/exercise')]
class ExerciseController extends AbstractController
{
    private const EXERCISE_NOT_EXISTS_MESSAGE = 'This exercise does not exist';
    private const UNAUTHORIZED_STATUS_MESSAGE = 'Please, sign in to add your own exercises';

    #[Route('/show/{slug}', name: 'show_exercise')]
    public function showExercise(
        string $slug,
        ShowExerciseService $showExerciseService,
        ExerciseViewsNumberCounter $exerciseViewsNumberCounter,
    ): Response {
        $exercise = $showExerciseService->findExerciseBySlug($slug);
        if ($exercise === null) {
            throw $this->createNotFoundException(self::EXERCISE_NOT_EXISTS_MESSAGE);
        }

        if ($this->getUser() !== null) {
            $exerciseViewsNumberCounter->incrementExerciseViewsNumber($exercise);
        }
        $exerciseDTO = ShowExerciseDTO::FromEntity($exercise);

        return $this->render('@app.src_dir/Exercise/Show/output.twig', [
            'exercise' => $exerciseDTO,
        ]);
    }

    #[Route('/add', name: 'add_exercise')]
    #[IsGranted(
        AuthenticatedVoter::IS_AUTHENTICATED,
        message: self::UNAUTHORIZED_STATUS_MESSAGE,
        statusCode: Response::HTTP_UNAUTHORIZED,
    )]
    public function addExercise(Request $request, AddExerciseService $addExerciseService): Response
    {
        $addExerciseForm = $this->createForm(AddExerciseForm::class);
        $addExerciseForm->handleRequest($request);
        if ($addExerciseForm->isSubmitted() && $addExerciseForm->isValid()) {
            try {
                $exercise = $addExerciseService->addExercise($addExerciseForm->getData(), $this->getUser()->getId());
                return $this->redirectToRoute('show_exercise', ['slug' => $exercise->getSlug()]);
            } catch (DuplicateUserExerciseTitleException $exception) {
                $addExerciseForm->get('title')->addError(new FormError($exception->getMessage()));
            } catch (UnableToSaveFileException $exception) {
                $addExerciseForm->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('@app.src_dir/Exercise/Add/output.twig', [
            'form' => $addExerciseForm,
        ]);
    }

    #[Route(path: '/search', name: 'search_exercise')]
    public function searchExercise(Request $request, SearchExerciseService $searchService): Response
    {
        $searchForm = $this->createForm(SearchForm::class);
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $exerciseEntities = $searchService->searchForExercises($searchForm->getData());
            $resultDTOs = [];
            foreach ($exerciseEntities as $exerciseEntity) {
                $resultDTOs[] = SearchExerciseResultDTO::fromEntity($exerciseEntity);
            }
        }

        return $this->render('@app.src_dir/Exercise/Search/output.twig', [
            'form' => $searchForm,
            'searchResults' => $resultDTOs ?? null,
        ]);
    }
}
