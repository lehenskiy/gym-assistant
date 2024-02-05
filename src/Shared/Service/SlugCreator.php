<?php

declare(strict_types=1);

namespace App\Shared\Service;

use Symfony\Component\String\Slugger\SluggerInterface;

class SlugCreator
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function createSlug(string ...$slugParts): string
    {
        $stringForSlug = implode('-', $slugParts);

        return $this->slugger
            ->slug($stringForSlug)
            ->lower()
            ->toString();
    }
}
