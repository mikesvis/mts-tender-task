<?php

namespace App\Entity;

use App\Repository\ImportStatRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImportStatRepository::class)
 */
class ImportStat
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $time_start;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $time_end;

    /**
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    private $count_total = 0;

    /**
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    private $count_error = 0;

    /**
     * @ORM\Column(type="decimal", precision=4, scale=1)
     */
    private $load_average = 0;

    /**
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    private $rows_deleted = 0;

    /**
     * @ORM\Column(type="integer", length=1)
     */
    private $is_full;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTimeStart(): ?int
    {
        return $this->time_start;
    }

    public function setTimeStart(int $time_start): self
    {
        $this->time_start = $time_start;

        return $this;
    }

    public function getTimeEnd(): ?int
    {
        return $this->time_end;
    }

    public function setTimeEnd(?int $time_end): self
    {
        $this->time_end = $time_end;

        return $this;
    }

    public function getCountTotal(): ?int
    {
        return $this->count_total;
    }

    public function setCountTotal(int $count_total): self
    {
        $this->count_total = $count_total;

        return $this;
    }

    public function getCountError(): ?int
    {
        return $this->count_error;
    }

    public function setCountError(int $count_error): self
    {
        $this->count_error = $count_error;

        return $this;
    }

    public function getLoadAverage(): ?string
    {
        return $this->load_average;
    }

    public function setLoadAverage(string $load_average): self
    {
        $this->load_average = $load_average;

        return $this;
    }

    public function getRowsDeleted(): ?int
    {
        return $this->rows_deleted;
    }

    public function setRowsDeleted(int $rows_deleted): self
    {
        $this->rows_deleted = $rows_deleted;

        return $this;
    }

    public function getIsFull(): ?int
    {
        return $this->is_full;
    }

    public function setIsFull(int $is_full): self
    {
        $this->is_full = $is_full;

        return $this;
    }
}
