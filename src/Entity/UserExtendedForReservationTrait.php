<?php

namespace PiedWeb\ReservationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait UserExtendedForReservationTrait
{
    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "user.firstname.short",
     *      maxMessage = "user.firstname.long"
     * )
     * @Assert\Regex(
     *      pattern="/^([a-zA-Z\w\p{L}]+(?:. |-| |'))*[a-zA-Z\w\p{L}]*$/iu",
     *      message = "user.firstname.invalid"
     * )
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "user.lastname.short",
     *      maxMessage = "user.lastname.long"
     * )
     * @Assert\Regex(
     *      pattern="/^([a-zA-Z\w\p{L}]+(?:. |-| |'))*[a-zA-Z\w\p{L}]*$/iu",
     *      message = "user.lastname.invalid"
     * )
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\Length(
     *      min = 2,
     *      max = 100,
     *      minMessage = "user.city.short",
     *      maxMessage = "user.city.long"
     * )
     * @Assert\Regex(
     *      pattern="/^([a-zA-Z0-9\w\p{L}]+(?:. |-| |'))*[a-zA-Z0-9\w\p{L}]*$/iu",
     *      message = "user.city.invalid"
     * )
     */
    private $city;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Date()
     * @Assert\LessThan(
     *      value = "-1 years",
     *      message = "user.dateOfBirth.young"
     * )
     */
    private $dateOfBirth;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Assert\Length(
     *      min = 2,
     *      max = 64,
     *      minMessage = "user.phone.short",
     *      maxMessage = "user.phone.long"
     * )
     */
    private $phone;

    /**
     * @ORM\OneToOne(targetEntity="PiedWeb\ReservationBundle\Entity\BasketInterface", mappedBy="user", cascade={"persist", "remove"})
     */
    private $basket;

    /**
     * @ORM\OneToMany(
     *      targetEntity="PiedWeb\ReservationBundle\Entity\OrderInterface",
     *      mappedBy="user",
     *      orphanRemoval=true,
     *      cascade={"persist", "remove"}
     * )
     */
    private $orders;

    /**
     * @return Collection|Orders[]
     */
    public function getOrders(): Collection
    {
        return null === $this->orders ? new ArrayCollection() : $this->orders;
    }

    public function addOrder($order): self
    {
        if (!$this->getOrders()->contains($order)) {
            $this->orders[] = $order;
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder($order): self
    {
        if ($this->getOrders()->contains($order)) {
            $this->orders->removeElement($order);
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?\DateTimeInterface $dateOfBirth): self
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getBasket()
    {
        return $this->basket;
    }

    public function setBasket($basket): self
    {
        $this->basket = $basket;

        // set (or unset) the owning side of the relation if necessary
        $newUser = null === $basket ? null : $this;
        if ($newUser !== $basket->getUser()) {
            $basket->setUser($newUser);
        }

        return $this;
    }
}
