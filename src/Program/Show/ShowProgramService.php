<?php

declare(strict_types=1);

namespace App\Program\Show;

use App\Shared\Entity\Program;
use App\Shared\Repository\ProgramRepository;

class ShowProgramService
{
    public function __construct(private ProgramRepository $programRepository)
    {
    }

    public function findBySlug(string $slug): ?Program
    {
        return $this->programRepository->findBySlugWithAllAssociations($slug);
    }
}
