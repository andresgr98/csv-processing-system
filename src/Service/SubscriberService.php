<?php

namespace App\Service;

use App\Repository\SubscriberRepository;

class SubscriberService
{
    public function __construct(private readonly SubscriberRepository $subscriberRepository) {}

    public function getSubscribers(array $filters): array
    {
        return $this->subscriberRepository->findByFilters($filters);
    }
}
