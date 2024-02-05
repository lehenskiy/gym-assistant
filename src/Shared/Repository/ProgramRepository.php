<?php

declare(strict_types=1);

namespace App\Shared\Repository;

use App\Shared\Entity\Program;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Program>
 *
 * @method Program|null find($id, $lockMode = null, $lockVersion = null)
 * @method Program|null findOneBy(array $criteria, array $orderBy = null)
 * @method Program[]    findAll()
 * @method Program[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgramRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Program::class);
    }

    public function save(Program $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Program $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findBySlugWithAllAssociations(string $slug): ?Program
    {
        return $this->createQueryBuilder('program')
            ->where('program.slug = :slug')
            ->setParameter('slug', strtolower($slug))
            ->innerJoin('program.author', 'author')
            ->leftJoin('program.programExercises', 'programExercises')
            ->leftJoin('programExercises.exercise', 'exercise')
            ->leftJoin('exercise.targetMuscles', 'targetMuscles')
            ->addSelect('author', 'programExercises', 'exercise', 'targetMuscles')
            ->addOrderBy('programExercises.position', 'ASC')
            ->addOrderBy('targetMuscles.muscle', 'ASC') // targetMuscles in fixed order
            ->getQuery()
            ->getOneOrNullResult();
    }
}
