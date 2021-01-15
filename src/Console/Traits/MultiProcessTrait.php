<?php

namespace App\Console\Traits;

use Symfony\Component\Process\Process;

/**
 * Трейт для управления паралельными процессами
 * 
 * Trait MultiProcessTrait
 * @package App\Console\Traits
 */
trait MultiProcessTrait
{
    private bool $stop = false;
    private array $channels = [];

    /**
     * Сигнал останова
     */
    private function setSignals(): void
    {
        pcntl_signal(SIGTERM, function ($signo) {
            $this->stop = true;
        });
    }

    /**
     * Количество процессов в работе
     * 
     * @return int
     */
    private function countChannels(): int
    {
        foreach ($this->channels as $key => $channel) {
            if ($channel->isTerminated()) {
                unset($this->channels[$key]);
            }
        }

        return count($this->channels);
    }

    /**
     * @param string $key
     * @param Process $process
     */
    private function addChannelByKey(string $key, Process $process): void
    {
        $this->channels[$key] = $process;
    }

    /**
     * Ждем завершения процессов обработки файлов
     */
    private function finishProcesses(): void
    {
        while (true) {
            $isProcessesRunning = $this->isStillRunning();
            usleep(8000);
            if (!$isProcessesRunning) {
                break;
            }
        }
    }

    /**
     * Проверяет, все ли процессы обработки файлов завершены
     * 
     * @return bool
     */
    private function isStillRunning(): bool
    {
        if (empty($this->channels)) {
            return false;
        }

        /** @var Process $channel */
        foreach ($this->channels as $key => $channel) {
            if ($channel->isTerminated()) {
                unset($this->channels[$key]);
            } else {
                usleep(8000);
                return true;
            }
        }

        return false;
    }

    /**
     * Количество процессов в системе по имени
     * 
     * @param $name
     * @return int
     */
    private function countProcessInstancesByName($name): int
    {
        $command = $this->getOsProcessesListCommand($name);

        $output = array();
        $returnValue = null;
        exec($command, $output, $returnValue);

        return count($output);
    }

    /**
     * Получение команды списка процессов с указанным именем взависимости от операционной системы
     * 
     * @param string $name
     * @return string
     */
    private function getOsProcessesListCommand(string $name): string
    {
        switch (strtolower(PHP_OS)) {
            case 'linux':
            case 'freebsd':
                $cmd = sprintf('ps aux | grep "%s" | grep -v "/bin/sh" | grep -v "grep"', $name);
                break;

            case 'winnt':
                $cmd = sprintf('tasklist -v | findstr /i "%s"', $name);
                break;

            default:
                throw new \RuntimeException(sprintf('Call %s unknown type of operating system "%s"', __METHOD__, strtolower(PHP_OS)));
        }
        
        return $cmd;
    }
}