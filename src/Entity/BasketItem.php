<?php

namespace PiedWeb\ReservationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="PiedWeb\ReservationBundle\Repository\BasketItemRepository")
 * @ORM\HasLifecycleCallbacks
 */
class BasketItem
{
    use OrderItemTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $addedAt;

    /**
     * @ORM\ManyToOne(targetEntity="PiedWeb\ReservationBundle\Entity\BasketInterface", inversedBy="basketItems")
     */
    private $basket;

    /**
     * @ORM\Column(type="boolean")
     */
    private $forMe;

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->addedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddedAt(): ?\DateTimeInterface
    {
        return $this->addedAt;
    }

    public function setAddedAt(\DateTimeInterface $addedAt): self
    {
        $this->addedAt = $addedAt;

        return $this;
    }

    public function getBasket(): ?Basket
    {
        return $this->basket;
    }

    public function setBasket(?Basket $basket): self
    {
        $this->basket = $basket;

        return $this;
    }

    public function getForMe(): ?bool
    {
        return $this->forMe;
    }

    public function setForMe(bool $forMe): self
    {
        $this->forMe = $forMe;

        return $this;
    }
}
