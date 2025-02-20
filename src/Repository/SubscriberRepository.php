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

    public function findByFilters(array $filters): array
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

        return $queryBuilder->getQuery()->getResult();
    }
}
