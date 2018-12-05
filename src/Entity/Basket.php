<?php

namespace PiedWeb\ReservationBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="PiedWeb\ReservationBundle\Repository\BasketRepository")
 */
class Basket
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="PiedWeb\ReservationBundle\Entity\BasketItemInterface", mappedBy="basket")
     */
    private $basketItems;

    /**
     * @ORM\OneToOne(targetEntity="PiedWeb\CMSBundle\Entity\UserInterface", inversedBy="basket", cascade={"persist", "remove"})
     */
    private $user;

    public function __construct()
    {
        $this->product = new ArrayCollection();
        $this->basketItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): float
    {
        $price = 0;

        foreach ($this->getBasketItems() as $b) {
            if (is_float($b->getPrice())) {
                $price = $price + $b->getPrice();
            }
        }

        return $price;
    }

    /**
     * @return Collection|BasketItem[]
     */
    public function getBasketItems(): Collection
    {
        return $this->basketItems;
    }

    public function getBasketItem(int $id)
    {
        foreach ($this->getBasketItems() as $b) {
            if ($b->getId() === $id) {
                return $b;
            }
        }

        return null;
    }

    /**
     * @param int $productId
     */
    public function issetItemForMe(int $productId)
    {
        foreach ($this->getBasketItems() as $b) {
            if ($b->getProduct()->getId() === $productId && true === $b->getForMe()) {
                return true;
            }
        }

        return false;
    }

    public function addBasketItem(BasketItem $basketItem): self
    {
        if (!$this->basketItems->contains($basketItem)) {
            $this->basketItems[] = $basketItem;
            $basketItem->setBasket($this);
        }

        return $this;
    }

    public function removeBasketItem(BasketItem $basketItem): self
    {
        if ($this->basketItems->contains($basketItem)) {
            $this->basketItems->removeElement($basketItem);
            // set the owning side to null (unless already changed)
            if ($basketItem->getBasket() === $this) {
                $basketItem->setBasket(null);
            }
        }

        return $this;
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
}
