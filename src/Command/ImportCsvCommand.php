<?php

namespace App\Command;

use App\Service\CsvProcessor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:import-csv')]
class ImportCsvCommand extends Command
{
    private CsvProcessor $csvProcessor;

    public function __construct(CsvProcessor $csvProcessor)
    {
        parent::__construct();
        $this->csvProcessor = $csvProcessor;
    }

    protected function configure()
    {
        $this
            ->setDescription('Import a CSV file and process its rows')
            ->addArgument('file', InputArgument::REQUIRED, 'CSV file path')
            ->addArgument('batch', InputArgument::OPTIONAL, 'Number of rows to process in a batch', 1000);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('file');
        $batchSize = (int) $input->getArgument('batch');

        if (!file_exists($filePath)) {
            $output->writeln('<error>The file does not exist.</error>');
            return Command::FAILURE;
        }
        if (!is_numeric($batchSize) || $batchSize <= 0) {
            $output->writeln('<error>Batch size must be a positive integer.</error>');
            return Command::FAILURE;
        }

        $this->csvProcessor->process($filePath, $batchSize);
        $output->writeln('<info>File processed succesfully.</info>');

        return Command::SUCCESS;
    }
}
