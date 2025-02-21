<?php

namespace App\MessageHandler;

use App\Entity\Subscriber;
use App\Message\ProcessCsvBatch;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ProcessCsvBatchHandler
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    public function __invoke(ProcessCsvBatch $message)
    {
        $connection = $this->entityManager->getConnection();

        $values = [];
        $params = [];

        foreach ($message->getRecords() as $record) {
            $values[] = "(?, ?, ?, ?)";
            $params[] = $record['name'];
            $params[] = $record['email'];
            $params[] = (int) $record['age'];
            $params[] = $record['address'];
        }

        $sql = "INSERT IGNORE INTO subscriber (name, email, age, address) VALUES " . implode(", ", $values);
        $connection->executeStatement($sql, $params);
    }
}
