<?php

namespace App\Message;

class ProcessCsvBatch
{
    public function __construct(private readonly array $records) {}

    public function getRecords(): array
    {
        return $this->records;
    }
}
