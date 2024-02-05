<?php

declare(strict_types=1);

namespace App\Shared\Repository;

use App\Shared\Entity\ProgramExercise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProgramExercise>
 *
 * @method ProgramExercise|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProgramExercise|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProgramExercise[]    findAll()
 * @method ProgramExercise[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgramExerciseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProgramExercise::class);
    }

    public function save(ProgramExercise $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ProgramExercise $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
