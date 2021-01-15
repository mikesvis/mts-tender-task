<?php

namespace App\Entity;

use App\Repository\WarehouseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WarehouseRepository::class)
 */
class Warehouse
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=5)
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=Remains::class, mappedBy="warehouse", orphanRemoval=true, cascade={"persist"})
     */
    private $remains;

    /**
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="warehouses", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $city;

    public function __construct()
    {
        $this->remains = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return Collection|Remains[]
     */
    public function getRemains(): Collection
    {
        return $this->remains;
    }

    public function addRemain(Remains $remain): self
    {
        if (!$this->remains->contains($remain)) {
            $this->remains[] = $remain;
            $remain->setWarehouse($this);
        }

        return $this;
    }

    public function removeRemain(Remains $remain): self
    {
        if ($this->remains->removeElement($remain)) {
            // set the owning side to null (unless already changed)
            if ($remain->getWarehouse() === $this) {
                $remain->setWarehouse(null);
            }
        }

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }
}
