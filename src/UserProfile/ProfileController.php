<?php

declare(strict_types=1);

namespace App\UserProfile;

use App\Shared\Entity\User;
use App\UserProfile\Edit\EditProfileDTO;
use App\UserProfile\Edit\EditProfileService;
use App\UserProfile\Edit\Exception\NewPasswordSameAsCurrentException;
use App\UserProfile\Edit\Exception\CurrentPasswordNotValidException;
use App\UserProfile\Edit\EditProfileForm;
use App\UserProfile\Show\ShowProfileService;
use DomainException;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profile')]
class ProfileController extends AbstractController
{
    private const USER_NOT_EXISTS_MESSAGE = 'This user does not exist';
    private const UNAUTHORIZED_STATUS_MESSAGE = 'Please, sign in to edit your profile';
    private const NOT_ALLOWED_TO_EDIT_THIS_PROFILE_MESSAGE = 'You are only allowed to edit your profile';

    #[Route('/show/{slug}', name: 'show_profile')]
    public function index(#[MapEntity] ?User $user, ShowProfileService $profileFinder): Response
    {
        if ($user === null) {
            throw $this->createNotFoundException(self::USER_NOT_EXISTS_MESSAGE);
        }

        $isCurrentUserPage = $user->getId() === $this->getUser()?->getId();
        $profile = $profileFinder->getProfile($user, $isCurrentUserPage);

        return $this->render('@app.src_dir/UserProfile/Show/output.twig', [
            'profile' => $profile,
            'isCurrentUserPage' => $isCurrentUserPage,
        ]);
    }

    #[Route('/edit/{slug}', name: 'edit_profile')]
    #[IsGranted(
        AuthenticatedVoter::IS_AUTHENTICATED,
        message: self::UNAUTHORIZED_STATUS_MESSAGE,
        statusCode: Response::HTTP_UNAUTHORIZED,
    )]
    public function edit(#[MapEntity] ?User $userToEdit, EditProfileService $userEditor, Request $request): Response
    {
        if ($userToEdit === null) {
            throw $this->createNotFoundException(self::USER_NOT_EXISTS_MESSAGE);
        }

        if ($this->getUser()->getId() !== $userToEdit->getId()) {
            throw $this->createAccessDeniedException(self::NOT_ALLOWED_TO_EDIT_THIS_PROFILE_MESSAGE);
        }

        $form = $this->createForm(EditProfileForm::class, $editDTO = EditProfileDTO::fromEntity($userToEdit));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $userEditor->editUser($userToEdit, $editDTO);
            } catch (CurrentPasswordNotValidException $exception) {
                $form->get('currentPassword')->addError(new FormError($exception->getMessage()));
            } catch (NewPasswordSameAsCurrentException $exception) {
                $form->get('newPassword')->addError(new FormError($exception->getMessage()));
            } catch (DomainException $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('@app.src_dir/UserProfile/Edit/output.twig', [
            'form' => $form,
        ]);
    }
}
