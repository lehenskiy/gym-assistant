<?php

declare(strict_types=1);

namespace App\Shared\Entity;

use App\Shared\Repository\UserWeightRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserWeightRepository::class)]
class UserWeight
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'weights')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $weight;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    public function __construct(User $user, int $weight)
    {
        $this->user = $user;
        $this->weight = $weight;

        $timeWithoutMicroseconds = DateTimeImmutable::createFromFormat(
            DATE_ATOM,
            (new DateTimeImmutable('now'))->format(DATE_ATOM),
        );
        $this->createdAt = $timeWithoutMicroseconds;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
