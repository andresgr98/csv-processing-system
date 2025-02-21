<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\ProcessCsvBatch;

class CsvProcessor
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MessageBusInterface $messageBus
    ) {}

    public function process(string $filePath, int $batchSize = 1000): void
    {
        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);

        $batch = [];

        foreach ($csv as $record) {
            $batch[] = $record;

            if (count($batch) >= $batchSize) {
                $this->messageBus->dispatch(new ProcessCsvBatch($batch));
                $batch = [];
            }
        }


        if (!empty($batch)) {
            $this->messageBus->dispatch(new ProcessCsvBatch($batch));
        }
    }
}
