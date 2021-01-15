<?php

namespace App\Entity;

use App\Repository\RemainsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RemainsRepository::class)
 */
class Remains
{

    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    private $product_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $count;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_update;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_import;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Warehouse::class, inversedBy="remains", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $warehouse;

    public function __construct()
    {

    }

    public function getProductId(): ?string
    {
        return $this->product_id;
    }

    public function setProductId(string $product_id): self
    {
        $this->product_id = $product_id;

        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): self
    {
        $this->count = $count;

        return $this;
    }

    public function getDateUpdate(): ?\DateTimeInterface
    {
        return $this->date_update;
    }

    public function setDateUpdate(?\DateTimeInterface $date_update): self
    {
        $this->date_update = $date_update;

        return $this;
    }

    public function getDateImport(): ?\DateTimeInterface
    {
        return $this->date_import;
    }

    public function setDateImport(?\DateTimeInterface $date_import): self
    {
        $this->date_import = $date_import;

        return $this;
    }

    public function getWarehouse(): ?Warehouse
    {
        return $this->warehouse;
    }

    public function setWarehouse(?Warehouse $warehouse): self
    {
        $this->warehouse = $warehouse;

        return $this;
    }
}
