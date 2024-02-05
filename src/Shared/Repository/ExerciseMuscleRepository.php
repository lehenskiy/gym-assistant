<?php

declare(strict_types=1);

namespace App\Shared\Repository;

use App\Shared\Entity\ExerciseMuscle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExerciseMuscle>
 *
 * @method ExerciseMuscle|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExerciseMuscle|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExerciseMuscle[]    findAll()
 * @method ExerciseMuscle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExerciseMuscleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExerciseMuscle::class);
    }

    public function save(ExerciseMuscle $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ExerciseMuscle $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
