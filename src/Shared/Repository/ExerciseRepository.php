<?php

declare(strict_types=1);

namespace App\Shared\Repository;

use App\Shared\Entity\Exercise;
use App\Shared\Entity\ExerciseMuscle;
use App\Shared\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Exercise>
 *
 * @method Exercise|null find($id, $lockMode = null, $lockVersion = null)
 * @method Exercise|null findOneBy(array $criteria, array $orderBy = null)
 * @method Exercise[]    findAll()
 * @method Exercise[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExerciseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Exercise::class);
    }

    public function save(Exercise $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Exercise $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getFiveMostPopularExercises(User $user): array
    {
        return $this->createQueryBuilder('exercise')
            ->where('exercise.author = :user')
            ->setParameter('user', $user)
            ->addOrderBy('DATE(exercise.createdAt)', 'ASC')
            ->addOrderBy('exercise.viewsNumber', 'DESC')
            ->addOrderBy('exercise.id')
            ->setMaxResults(5) // TODO use FETCH FIRST 5 ROWS ONLY, create indices on ORDER BY fields
            ->getQuery()
            ->getResult();
    }

    public function findBySlugWithAuthorAndTargetMuscles(string $slug): ?Exercise
    {
        return $this->createQueryBuilder('exercise')
            ->where('exercise.slug = :slug')
            ->setParameter('slug', strtolower($slug))
            ->innerJoin('exercise.author', 'author')
            ->leftJoin('exercise.targetMuscles', 'targetMuscles')
            ->addSelect('author', 'targetMuscles')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getBySearchParams(
        ?string $titleToSearch,
        ?string $authorToSearch,
        ?array $difficultiesForFilter,
        ?bool $withImage,
        ?array $targetMusclesForFilter
    ): array {
        $queryBuilder = $this->createQueryBuilder('exercise');

        $this->addSearchTitleCondition($queryBuilder, $titleToSearch);
        $this->joinAuthorWithFilter($queryBuilder, $authorToSearch);
        $this->addDifficultyFilter($queryBuilder, $difficultiesForFilter);
        $this->addImagePresenceFilter($queryBuilder, $withImage);
        $this->joinTargetMusclesWithFilter($queryBuilder, $targetMusclesForFilter);
        $this->addOrder($queryBuilder);

        return $queryBuilder->getQuery()
            ->getResult();
    }

    private function addSearchTitleCondition(QueryBuilder $queryBuilder, ?string $titleToSearch): void
    {
        if ($titleToSearch !== null) {
            $queryBuilder
                ->andWhere('LOWER(exercise.title) LIKE :titleToSearch')
                ->setParameter('titleToSearch', '%' . strtolower($titleToSearch) . '%');
        }
    }

    private function joinAuthorWithFilter(QueryBuilder $queryBuilder, ?string $authorToFilter): void
    {
        if ($authorToFilter !== null) {
            $queryBuilder
                ->innerJoin(
                    'exercise.author',
                    'author',
                    Query\Expr\Join::WITH,
                    'LOWER(author.username) LIKE :authorToSearch'
                )
                ->setParameter('authorToSearch', '%' . strtolower($authorToFilter) . '%');
        } else {
            $queryBuilder->leftJoin('exercise.author', 'author');
        }

        $queryBuilder->addSelect('author');
    }

    private function addDifficultyFilter(QueryBuilder $queryBuilder, ?array $difficultiesForFilter): void
    {
        if ($difficultiesForFilter !== null && $difficultiesForFilter !== []) {
            $queryBuilder
                ->andWhere('exercise.difficulty IN (:difficultiesForFilter)')
                ->setParameter('difficultiesForFilter', $difficultiesForFilter);
        }
    }

    private function addImagePresenceFilter(QueryBuilder $queryBuilder, ?bool $withImage): void
    {
        if ($withImage !== null && $withImage !== false) {
            $queryBuilder->andWhere('exercise.imageFilename IS NOT NULL');
        }
    }

    private function joinTargetMusclesWithFilter(QueryBuilder $queryBuilder, ?array $targetMusclesForFilter): void
    {
        if ($targetMusclesForFilter !== null && $targetMusclesForFilter !== []) {
            $subqueryToFilterTargetMuscles = $this->getEntityManager()->createQueryBuilder()
                ->select('IDENTITY(exerciseMuscle.exercise)')
                ->from(ExerciseMuscle::class, 'exerciseMuscle')
                ->groupBy('exerciseMuscle.exercise')
                ->where('exerciseMuscle.muscle in (:targetMusclesForFilter)')
                ->having('COUNT(exerciseMuscle.muscle) = :targetMusclesNumber')
                ->getDQL();

            $queryBuilder
                ->andWhere($queryBuilder->expr()->in('exercise.id', $subqueryToFilterTargetMuscles))
                ->setParameter('targetMusclesForFilter', $targetMusclesForFilter)
                ->setParameter('targetMusclesNumber', count($targetMusclesForFilter));
        }

        $queryBuilder->leftJoin('exercise.targetMuscles', 'targetMuscles');
        $queryBuilder->addSelect('targetMuscles');
    }

    private function addOrder(QueryBuilder $queryBuilder): void
    {
        $queryBuilder->addOrderBy('exercise.viewsNumber', 'DESC');
    }
}
