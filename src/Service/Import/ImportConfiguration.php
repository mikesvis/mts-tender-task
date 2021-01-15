<?php

namespace App\Service\Import;

use Symfony\Component\Console\Input\InputInterface;

class ImportConfiguration
{
    public string $path;
    public string $type;
    public int $max_threads;
    public int $threadsPause;
    public int $dbCleanStepCount;
    public int $dbCleanPause;
    public int $start;
    
    public function __construct(InputInterface $input)
    {
        $this->path = (string)$input->getOption('path');
        $this->type = (string)$input->getOption('type');
        $this->max_threads = (int)$input->getOption('max-threads');
        $this->threadsPause = (int)$input->getOption('threads-pause') * 1000;
        $this->dbCleanStepCount = (int)$input->getOption('db-clean-step-count');
        $this->dbCleanPause = (int)$input->getOption('db-clean-pause') * 1000;

        $this->start = time();
    }
}