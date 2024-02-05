<?php

declare(strict_types=1);

namespace App\Shared\Entity;

use App\Shared\Repository\ProgramRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProgramRepository::class)]
#[ORM\UniqueConstraint(fields: ['title', 'author'])] // unique title for every author, search by title or title+author
#[ORM\Index(fields: ['author'])] // search by author
class Program
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    private string $title;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private User $author;

    #[ORM\Column(length: 60, unique: true)]
    private string $slug;

    #[ORM\Column(length: 511, nullable: true)]
    private ?string $description;

    #[ORM\OneToMany(
        mappedBy: 'program',
        targetEntity: ProgramExercise::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true,
    )]
    private Collection $programExercises;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageFilename;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    public function __construct(string $title, User $author, string $slug)
    {
        $this->title = $title;
        $this->author = $author;
        $this->slug = $slug;

        $timeWithoutMicroseconds = DateTimeImmutable::createFromFormat(
            DATE_ATOM,
            (new DateTimeImmutable('now'))->format(DATE_ATOM),
        );
        $this->createdAt = $timeWithoutMicroseconds;

        $this->programExercises = new ArrayCollection();
    }

    public function setAdditionalInfo(?string $description, ?string $imageFilename): self
    {
        $this->description = $description;
        $this->imageFilename = $imageFilename;

        return $this;
    }

    public function addExerciseToLastPosition(Exercise $exercise): self
    {
        $lastProgramExercisePosition = 0; // stub, TODO to implement (with /program/compose pr)
        $programExercise = new ProgramExercise($this, $exercise, $lastProgramExercisePosition + 1);
        $this->programExercises->add($programExercise);

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getProgramExercises(): Collection
    {
        return $this->programExercises;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getImageFilename(): ?string
    {
        return $this->imageFilename;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
