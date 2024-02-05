<?php

declare(strict_types=1);

namespace App\Shared\Repository;

use App\Shared\Entity\UserWeight;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserWeight>
 *
 * @method UserWeight|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserWeight|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserWeight[]    findAll()
 * @method UserWeight[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserWeightRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserWeight::class);
    }

    public function save(UserWeight $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserWeight $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
