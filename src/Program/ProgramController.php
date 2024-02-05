<?php

declare(strict_types=1);

namespace App\Program;

use App\Program\Show\ShowProgramDTO;
use App\Program\Show\ShowProgramService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/program')]
class ProgramController extends AbstractController
{
    private const PROGRAM_NOT_EXISTS_MESSAGE = 'This program does not exist';

    #[Route('/show/{slug}', name: 'show_program')]
    public function index(string $slug, ShowProgramService $showProgramService): Response
    {
        $program = $showProgramService->findBySlug($slug);
        if ($program === null) {
            throw $this->createNotFoundException(self::PROGRAM_NOT_EXISTS_MESSAGE);
        }

        $programDTO = ShowProgramDTO::fromEntity($program);

        return $this->render('@app.src_dir/Program/Show/output.twig', [
            'program' => $programDTO,
        ]);
    }
}
