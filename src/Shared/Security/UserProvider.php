<?php

declare(strict_types=1);

namespace App\Shared\Security;

use App\Shared\Entity\User;
use App\Shared\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    // prevent user loading from database - return user from session
    public function refreshUser(UserInterface $user): UserInterface
    {
        return $user;
    }

    public function supportsClass(string $class): bool {
        return $class === User::class;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $result = $this->userRepository->createQueryBuilder('user')
            ->andWhere('user.email = :identifier')
            ->setParameter('identifier', $identifier)
            ->getQuery()
            ->getOneOrNullResult();

        if ($result === null) {
            throw new UserNotFoundException();
        }

        return $result;
    }
}
