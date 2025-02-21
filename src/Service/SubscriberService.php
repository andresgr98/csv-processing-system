<?php

namespace App\Service;

use App\Repository\SubscriberRepository;

class SubscriberService
{
    public function __construct(private readonly SubscriberRepository $subscriberRepository) {}

    public function getSubscribers(array $filters, int $page, int $limit)
    {
        return $this->subscriberRepository->findByFilters($filters, $page, $limit);
    }

    public function countSubscribers(array $filters)
    {
        return $this->subscriberRepository->countByFilters($filters);
    }
}
