<?php

namespace PiedWeb\ReservationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use PiedWeb\CMSBundle\Entity\PageInterface;

/**
 * @ORM\MappedSuperclass
 */
class Product implements ProductInterface
{
    use PriceTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="PiedWeb\CMSBundle\Entity\PageInterface", inversedBy="products")
     */
    protected $page;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $departureDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $time;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $participantNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $specifications;

    /**
     * @ORM\OneToMany(targetEntity="PiedWeb\ReservationBundle\Entity\OrderItemInterface", mappedBy="product")
     */
    protected $participants;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPage(): ?PageInterface
    {
        return $this->page;
    }

    public function setPage(?PageInterface $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function getDepartureDate(): ?\DateTimeInterface
    {
        return $this->departureDate;
    }

    public function setDepartureDate(\DateTimeInterface $departureDate): self
    {
        $this->departureDate = $departureDate;

        return $this;
    }

    public function getTime(): ?string
    {
        return 1002 == $this->time ? '1/2' : $this->time;
    }

    public function getTimeUnit(): string
    {
        if ('1/2' == $this->getTime() || 1 == $this->getTime()) {
            return 'journée';
        } elseif ($this->getTime() > 1) {
            return 'journées';
        }
    }

    public function setTime($time): self
    {
        $this->time = '1/2' == $time ? 1002 : $time;

        return $this;
    }

    public function getParticipantNumber(): ?int
    {
        return $this->participantNumber;
    }

    public function setParticipantNumber(?int $participantNumber): self
    {
        $this->participantNumber = $participantNumber;

        return $this;
    }

    public function getSpecifications(): ?string
    {
        return $this->specifications;
    }

    public function setSpecifications(?string $specifications): self
    {
        $this->specifications = $specifications;

        return $this;
    }

    /**
     * @return Collection|participants[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    /**
     * @param OrderItem $participants
     */
    public function addBasketItem($participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
            $participant->setProduct($this);
        }

        return $this;
    }

    /**
     * @param OrderItem $participants
     */
    public function removeBasketItem($participant): self
    {
        if ($this->participants->contains($participant)) {
            $this->participants->removeElement($participant);
            if ($participant->getProduct() === $this) {
                $participant->setProduct(null);
            }
        }

        return $this;
    }
}
