<?php

namespace PiedWeb\ReservationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

trait PriceTrait
{
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $priceHt;

    /**
     * @ORM\Column(type="float", options={"default" : 0})
     */
    private $vat = 0;

    public function getPrice(): ?float
    {
        return $this->priceHt + ($this->priceHt * ($this->vat / 100));
    }

    public function getPriceHt(): ?float
    {
        return $this->priceHt;
    }

    public function setPriceHt(?float $price): self
    {
        $this->priceHt = $price;

        return $this;
    }

    public function getVat(): ?float
    {
        return $this->vat;
    }

    public function setVat(?float $vat): self
    {
        $this->vat = $vat;

        return $this;
    }
}
