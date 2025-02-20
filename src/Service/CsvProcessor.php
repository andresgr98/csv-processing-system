<?php

namespace App\Service;

use App\Entity\Subscriber;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\ProcessCsvRow;

class CsvProcessor
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MessageBusInterface $messageBus
    ) {}

    public function process(string $filePath): void
    {
        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $record) {
            $this->messageBus->dispatch(new ProcessCsvRow($record));
        }
    }
}
