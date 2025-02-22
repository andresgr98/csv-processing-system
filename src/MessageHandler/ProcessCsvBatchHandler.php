<?php

namespace App\MessageHandler;

use App\Message\ProcessCsvBatch;
use App\Repository\SubscriberRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ProcessCsvBatchHandler
{
    public function __construct(private readonly SubscriberRepository $subscriberRepository) {}

    public function __invoke(ProcessCsvBatch $message): void
    {
        $values = [];
        $params = [];

        foreach ($message->getRecords() as $record) {
            $values[] = "(?, ?, ?, ?)";
            $params[] = $record['name'];
            $params[] = $record['email'];
            $params[] = (int) $record['age'];
            $params[] = $record['address'];
        }

        $this->subscriberRepository->persistBatch($values, $params);
    }
}
