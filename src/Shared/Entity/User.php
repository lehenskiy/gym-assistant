<?php

declare(strict_types=1);

namespace App\Shared\Entity;

use App\Shared\Enum\UserGender;
use App\Shared\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Serializable;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, Serializable, EquatableInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private string $email;

    #[ORM\Column(length: 20, unique: true)]
    private string $username;

    #[ORM\Column]
    private string $password;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private bool $public;

    #[ORM\Column(length: 20, unique: true)]
    private string $slug;

    #[ORM\Column(type: Types::SMALLINT, nullable: true, enumType: UserGender::class)]
    private ?UserGender $gender;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $birthDate;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $height;

    #[ORM\OneToMany(
        mappedBy: 'user',
        targetEntity: UserWeight::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true,
    )]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    private Collection $weights;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $goalWeight;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    public function __construct(string $email, string $username, string $slug)
    {
        $this->email = $email;
        $this->username = $username;
        $this->slug = $slug;
        $this->public = false;

        $timeWithoutMicroseconds = DateTimeImmutable::createFromFormat(
            DATE_ATOM,
            (new DateTimeImmutable('now'))->format(DATE_ATOM),
        );
        $this->createdAt = $timeWithoutMicroseconds;

        $this->weights = new ArrayCollection();
    }

    public function setHashedPassword(string $hashedPassword): self
    {
        $this->password = $hashedPassword;

        return $this;
    }

    public function updatePersonalInfo(
        bool $public,
        ?UserGender $gender,
        ?DateTimeImmutable $birthDate,
        ?int $height,
        ?int $goalWeight,
    ): self {
        $this->public = $public;
        $this->gender = $gender;
        $this->birthDate = $birthDate;
        $this->height = $height;
        $this->goalWeight = $goalWeight;

        return $this;
    }

    public function updateCurrentWeight(int $weight): self
    {
        $userWeight = new UserWeight($this, $weight);
        $this->weights->add($userWeight);

        return $this;
    }

    public function changeEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getPublic(): ?bool
    {
        return $this->public;
    }

    public function getGender(): ?UserGender
    {
        return $this->gender;
    }

    public function getBirthDate(): ?DateTimeImmutable
    {
        return $this->birthDate;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function getWeights(): Collection
    {
        return $this->weights;
    }

    public function getGoalWeight(): ?int
    {
        return $this->goalWeight;
    }

    /**
     * A visual identifier that represents this user.
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->getUsername();
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    // preventing of all user data is serialized into session
    public function serialize(): string
    {
        return serialize($this);
    }

    public function unserialize(string $data): void
    {
        $unserializedData = unserialize($data);

        [
            'id' => $this->id,
            'roles' => $this->roles,
            'username' => $this->username,
        ] = $unserializedData;
    }

    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'roles' => $this->roles,
            'username' => $this->username,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->id = $data['id'];
        $this->roles = $data['roles'];
        $this->username = $data['username'];
    }

    public function isEqualTo(UserInterface $user): bool
    {
        return ($user instanceof self && $this == $user);
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }
}
