<?php

namespace App\Service\Statistics;

use App\Service\Import\ImportConfiguration;
use App\Service\Import\ImportService;
use JetBrains\PhpStorm\Pure;

/**
 * Class Result
 * @package App\Service\Statistics
 */
class Result
{
    public int $timeStart;
    public int $is_full;
    public int $deleted = 0;

    public function __construct(ImportConfiguration $config)
    {
        $this->timeStart = $config->start;
        $this->is_full = (int)($config->type === ImportService::TYPE_FULL);
    }

    /**
     * Получить системную нагрузку
     * 
     * @return float
     */
    #[Pure] public function getLoadAverage(): float
    {
        $loads = sys_getloadavg();
        return $loads[0];
    }
}