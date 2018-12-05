<?php

namespace PiedWeb\ReservationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="PiedWeb\ReservationBundle\Repository\OrderItemRepository")
 */
class OrderItem
{
    use OrderItemTrait;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $canceledAt;

    /**
     * @ORM\ManyToOne(targetEntity="PiedWeb\ReservationBundle\Entity\OrderInterface", inversedBy="orderItem")
     * @ORM\JoinColumn(nullable=false)
     */
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity="PiedWeb\ReservationBundle\Entity\ProductInterface", inversedBy="participants")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCanceledAt(): ?\DateTimeInterface
    {
        return $this->canceledAt;
    }

    public function setCanceledAt(?\DateTimeInterface $canceledAt): self
    {
        $this->canceledAt = $canceledAt;

        return $this;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): self
    {
        $this->order = $order;

        return $this;
    }
}
