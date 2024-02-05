<?php

declare(strict_types=1);

namespace App\Shared\Entity;

use App\Shared\Enum\Muscle;
use App\Shared\Repository\ExerciseRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExerciseRepository::class)]
#[ORM\UniqueConstraint(fields: ['title', 'author'])] // unique title for every author, search by title or title+author
#[ORM\Index(fields: ['author'])] // search by author
class Exercise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private string $title;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private User $author;

    #[ORM\Column(length: 40, unique: true)]
    private string $slug;

    #[ORM\Column]
    private int $viewsNumber;

    #[ORM\Column(length: 511, nullable: true)]
    private ?string $description;

    #[ORM\Column(length: 1023, nullable: true)]
    private ?string $executionTechnique;

    #[ORM\Column(length: 511, nullable: true)]
    private ?string $executionTips;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $difficulty;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageFilename;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\OneToMany(
        mappedBy: 'exercise',
        targetEntity: ExerciseMuscle::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true,
    )]
    private Collection $targetMuscles;

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
        $this->viewsNumber = 0;

        $this->targetMuscles = new ArrayCollection();
    }

    /**@param $targetMuscles array<Muscle> */
    public function setAdditionalInfo(
        ?string $description,
        ?string $executionTechnique,
        ?string $executionTips,
        ?int $difficulty,
        ?string $imageFilename,
        array $targetMuscles,
    ): self {
        $this->description = $description;
        $this->executionTechnique = $executionTechnique;
        $this->executionTips = $executionTips;
        $this->difficulty = $difficulty;
        $this->imageFilename = $imageFilename;

        $this->targetMuscles->clear();
        foreach ($targetMuscles as $targetMuscle) {
            $exerciseMuscle = new ExerciseMuscle($this, $targetMuscle);
            $this->targetMuscles->add($exerciseMuscle);
        }

        return $this;
    }

    public function updateViewsNumber(int $viewsNumber): self
    {
        $this->viewsNumber = $viewsNumber;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getViewsNumber(): int
    {
        return $this->viewsNumber;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getExecutionTechnique(): ?string
    {
        return $this->executionTechnique;
    }

    public function getExecutionTips(): ?string
    {
        return $this->executionTips;
    }

    public function getDifficulty(): ?int
    {
        return $this->difficulty;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getImageFilename(): ?string
    {
        return $this->imageFilename;
    }

    public function getTargetMuscles(): Collection
    {
        return $this->targetMuscles;
    }
}
