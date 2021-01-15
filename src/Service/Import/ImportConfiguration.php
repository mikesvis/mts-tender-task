<?php

namespace App\Service\Import;

use Symfony\Component\Console\Input\InputInterface;

/**
 * Class ImportConfiguration
 * @package App\Service\Import
 */
class ImportConfiguration
{
    /**
     * @var string Опция "path"
     */
    public string $path;

    /**
     * @var string Опция "type"
     */
    public string $type;

    /**
     * @var int Опция "max-threads"
     */
    public int $max_threads;

    /**
     * @var int Опция "threads-pause"
     */
    public int $threadsPause;

    /**
     * @var int Опция "db-clean-step-count"
     */
    public int $dbCleanStepCount;

    /**
     * @var int Опция "db-clean-pause"
     */
    public int $dbCleanPause;

    /**
     * @var int Старт timestamp
     */
    public int $start;

    /**
     * ImportConfiguration constructor.
     * @param InputInterface $input
     */
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