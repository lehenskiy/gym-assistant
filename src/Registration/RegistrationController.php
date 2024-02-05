<?php

declare(strict_types=1);

namespace App\Registration;

use App\Shared\Exception\DuplicateUserException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/signup', name: 'signup')]
    public function index(
        Request $request,
        Security $security,
        RegistrationService $registrationService,
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('show_home');
        }

        $registrationForm = $this->createForm(RegistrationForm::class);
        $registrationForm->handleRequest($request);
        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
            try {
                $user = $registrationService->register($registrationForm->getData());
            } catch (DuplicateUserException $exception) {
                $registrationForm->addError(new FormError($exception->getMessage()));

                return $this->render('@app.src_dir/Registration/output.twig', [
                    'form' => $registrationForm,
                ]);
            }

            $security->login($user);

            return $this->redirectToRoute('show_home');
        }

        return $this->render('@app.src_dir/Registration/output.twig', [
            'form' => $registrationForm,
        ]);
    }
}
