<?php

namespace App\MessageHandler;

use App\Entity\Subscriber;
use App\Message\ProcessCsvRow;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

#[AsMessageHandler]
class ProcessCsvRowHandler
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    public function __invoke(ProcessCsvRow $message)
    {
        $data = $message->getData();

        $subscriber = new Subscriber();
        $subscriber->setName($data['name']);
        $subscriber->setEmail($data['email']);
        $subscriber->setAge((int) $data['age']);
        $subscriber->setAddress($data['address']);

        $this->entityManager->persist($subscriber);
        $this->entityManager->flush();
    }
}
