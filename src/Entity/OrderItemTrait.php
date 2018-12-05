<?php

namespace PiedWeb\ReservationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait OrderItemTrait
{
    use PriceTrait;

    /**
     * @ORM\ManyToOne(targetEntity="PiedWeb\ReservationBundle\Entity\ProductInterface")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Date()
     * @Assert\LessThan("-1 years")
     */
    private $dateOfBirth;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "Votre prénom semble trop court",
     *      maxMessage = "Votre prénom semble trop long"
     * )
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "Votre prénom semble trop court",
     *      maxMessage = "Votre prénom semble trop long"
     * )
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     *
     * @Assert\Length(
     *      min = 2,
     *      max = 64,
     *      minMessage = "Votre numéro de téléphone semble trop court",
     *      maxMessage = "Votre numéro de téléphone semble trop long"
     * )
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Email(
     *     message = "Votre adresse email '{{ value }}' n'est pas valide.",
     *     checkMX = true
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\Length(
     *      min = 2,
     *      max = 100,
     *      minMessage = "Le nom de votre ville semble trop court",
     *      maxMessage = "Le nom de votre ville semble trop long"
     * )
     */
    private $city;

    public function __toString()
    {
        return $this->getHumanId();
    }

    public function getHumanId()
    {
        return $this->getProduct()->getName().' ('.$this->getFirstname().' '.$this->getLastname().')';
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function setProduct($product): self
    {
        $this->product = $product;

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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

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
}
