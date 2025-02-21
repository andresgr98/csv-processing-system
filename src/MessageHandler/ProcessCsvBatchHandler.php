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
        $batch = [];

        foreach ($message->getRecords() as $record) {
            $subscriber = new Subscriber();
            $subscriber->setName($record['name']);
            $subscriber->setEmail($record['email']);
            $subscriber->setAge((int) $record['age']);
            $subscriber->setAddress($record['address']);

            $this->entityManager->persist($subscriber);
            $batch[] = $subscriber;
        }

        $this->entityManager->flush();
        $this->entityManager->clear();
    }
}
