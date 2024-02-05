<?php

declare(strict_types=1);

namespace App\UserProfile\Show\DTO;

use App\Shared\Entity\UserWeight;
use DateTimeImmutable;

readonly class UserWeightDTO
{
    public int $weight;
    public DateTimeImmutable $creationDate;

    public function __construct(array $weightData)
    {
        [
            'weight' => $this->weight,
            'creationDate' => $this->creationDate,
        ] = $weightData;
    }

    public static function fromEntity(UserWeight $weight): self
    {
        $weightData = [
            'weight' => $weight->getWeight(),
            'creationDate' => $weight->getCreatedAt(),
        ];

        return new self($weightData);
    }
}
