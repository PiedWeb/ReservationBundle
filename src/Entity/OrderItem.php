<?php

namespace PiedWeb\ReservationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
class OrderItem implements OrderItemInterface
{
    use OrderItemTrait;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $canceledAt;

    /**
     * @ORM\ManyToOne(targetEntity="PiedWeb\ReservationBundle\Entity\OrderInterface", inversedBy="orderItems")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $order;

    /**
     * @ORM\ManyToOne(targetEntity="PiedWeb\ReservationBundle\Entity\ProductInterface", inversedBy="participants")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $product;

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
