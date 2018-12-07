<?php

namespace PiedWeb\ReservationBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="PiedWeb\ReservationBundle\Repository\OrderRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="`Order`")
 */
class Order implements OrderInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="PiedWeb\CMSBundle\Entity\UserInterface", inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(
     *      targetEntity="PiedWeb\ReservationBundle\Entity\OrderItemInterface",
     *      mappedBy="order",
     *      orphanRemoval=true,
     *      cascade={"persist", "remove"}
     * )
     */
    private $orderItems;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $paymentMethod;

    /**
     * @ORM\Column(type="datetime")
     */
    private $orderedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $canceledAt;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $refund;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * todo: supprimer cet élément
     */
    private $paid;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $paidAt;

    public function __toString()
    {
        return null === $this->orderedAt ? (string) $this->id : $this->id.' - '.$this->orderedAt->format('Y-m-d H:i:s');
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->orderedAt = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        if (null !== $this->canceledAt) {
            $products = $this->getOrderItems();
            foreach ($products as $p) {
                $p->setCanceledAt($this->canceledAt);
            }
        }
    }

    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
    }

    public function getPrice(): float
    {
        $price = 0;

        foreach ($this->getOrderItems() as $b) {
            if (is_float($b->getPrice())) {
                $price = $price + $b->getPrice();
            }
        }

        return $price;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|OrderItems[]
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem($orderItem): self
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems[] = $orderItem;
            $orderItem->setOrder($this);
        }

        return $this;
    }

    public function removeOrderItem($orderItem): self
    {
        if ($this->orderItems->contains($orderItem)) {
            $this->orderItems->removeElement($orderItem);
            // set the owning side to null (unless already changed)
            if ($orderItem->getOrder() === $this) {
                $orderItem->setOrder(null);
            }
        }

        return $this;
    }

    public function getPaymentMethod(): ?int
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(int $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function getPaid(): ?bool
    {
        return $this->paid;
    }

    public function setPaid(bool $paid): self
    {
        $this->paid = $paid;

        return $this;
    }

    public function getOrderedAt(): ?\DateTimeInterface
    {
        return $this->orderedAt;
    }

    public function setOrderedAt(\DateTimeInterface $orderedAt): self
    {
        $this->orderedAt = $orderedAt;

        return $this;
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

    public function getRefund(): ?bool
    {
        return $this->refund;
    }

    public function setRefund(?bool $refund): self
    {
        $this->refund = $refund;

        return $this;
    }

    public function getPaidAt(): ?\DateTimeInterface
    {
        return $this->paidAt;
    }

    public function setPaidAt(?\DateTimeInterface $paidAt): self
    {
        $this->paidAt = $paidAt;

        return $this;
    }
}
