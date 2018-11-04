<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\RatesFetcher;

class UpdateDatabaseCommand extends Command
{
    
    private $ratesFetcher;
    
    public function __construct(RatesFetcher $rf) {
        $this->ratesFetcher = $rf;
        
        parent::__construct();
    }


    protected function configure()
    {
        $this->setName('app:update-database')
             ->setDescription('Updates the currency rates from NBP API.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Updating the database...');
        $this->ratesFetcher->updateFromNbp();
        $output->writeln('Update done.');

    }
}