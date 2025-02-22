<?php

namespace App\Repository;

use App\Entity\Subscriber;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SubscriberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscriber::class);
    }

    public function findByFilters(array $filters, int $page = 1, int $limit = 50): array
    {
        $queryBuilder = $this->createQueryBuilder('s');

        if (!empty($filters['name'])) {
            $queryBuilder->andWhere('s.name LIKE :name')
                ->setParameter('name', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['email'])) {
            $queryBuilder->andWhere('s.email LIKE :email')
                ->setParameter('email', '%' . $filters['email'] . '%');
        }

        if (!empty($filters['age'])) {
            $queryBuilder->andWhere('s.age = :age')
                ->setParameter('age', $filters['age']);
        }

        if (!empty($filters['address'])) {
            $queryBuilder->andWhere('s.address LIKE :address')
                ->setParameter('address', '%' . $filters['address'] . '%');
        }

        $queryBuilder->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return $queryBuilder->getQuery()->getResult();
    }

    public function countByFilters(array $filters): int
    {
        $queryBuilder = $this->createQueryBuilder('s');

        if (!empty($filters['name'])) {
            $queryBuilder->andWhere('s.name LIKE :name')
                ->setParameter('name', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['email'])) {
            $queryBuilder->andWhere('s.email LIKE :email')
                ->setParameter('email', '%' . $filters['email'] . '%');
        }

        if (!empty($filters['age'])) {
            $queryBuilder->andWhere('s.age = :age')
                ->setParameter('age', $filters['age']);
        }

        if (!empty($filters['address'])) {
            $queryBuilder->andWhere('s.address LIKE :address')
                ->setParameter('address', '%' . $filters['address'] . '%');
        }

        return (int) $queryBuilder->select('COUNT(s.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function persistBatch(array $values, array $params): void
    {
        $sql = "INSERT IGNORE INTO subscriber (name, email, age, address) VALUES " . implode(", ", $values);

        $this->getEntityManager()->getConnection()->executeStatement($sql, $params);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();
    }
}
