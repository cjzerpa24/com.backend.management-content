<?php

namespace App\Repository;

use App\Entity\Content;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Content>
 */
class ContentRepository extends ServiceEntityRepository implements ContentRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Content::class);
    }

    public function findById(int $id): ?Content
    {
        return $this->find($id);
    }

    /**
     * @throws \Exception
     */
    public function findByCriteria(array $criteria, ?array $operator = null): array
    {
        $page = $criteria['page'] ?? 1;
        $limit = $criteria['limit'] ?? 10;

        unset($criteria['page'], $criteria['limit']);

        // Filter out null criteria
        $criteria = array_filter($criteria, function ($value) {
            return null !== $value;
        });

        $queryBuilder = $this->createQueryBuilder('c');

        foreach ($criteria as $field => $value) {
            $op = $operator[$field] ?? 'LIKE';

            if (is_object($value)) {
                $queryBuilder->andWhere("c.$field = :$field")
                    ->setParameter($field, $value);
            } else {
                if ('LIKE' === $op) {
                    $queryBuilder->andWhere("c.$field LIKE :$field")
                        ->setParameter($field, '%'.$value.'%');
                } else {
                    $queryBuilder->andWhere("c.$field $op :$field")
                        ->setParameter($field, $value);
                }
            }
        }

        $queryBuilder->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $paginator = new Paginator($queryBuilder);

        return [
            'total' => $paginator->count(),
            'data' => $paginator->getIterator(),
        ];
    }

    public function save(Content $content): void
    {
        $this->getEntityManager()->persist($content);
        $this->getEntityManager()->flush();
    }

    public function update(Content $content): void
    {
        $this->getEntityManager()->persist($content);
        $this->getEntityManager()->flush();
    }

    public function remove(Content $content): void
    {
        $this->getEntityManager()->remove($content);
        $this->getEntityManager()->flush();
    }


}
