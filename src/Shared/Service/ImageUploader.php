<?php

declare(strict_types=1);

namespace App\Shared\Service;

use App\Shared\Exception\UnableToSaveFileException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageUploader
{
    public function __construct(
        private $imagesDirectory,
        private SluggerInterface $slugger,
    ) {
    }

    /**@throws UnableToSaveFileException */
    public function upload(UploadedFile $file, string $prefix): string
    {
        $safeFilename = $this->slugger->slug($prefix . (new \DateTimeImmutable())->format(DATE_ATOM));

        try {
            $filename = $safeFilename . '.' . $file->guessExtension();
            $file->move($this->imagesDirectory, $filename);
        } catch (FileException $exception) {
            throw new UnableToSaveFileException('Unable to save file', previous: $exception);
        }

        return $filename;
    }
}
