<?php

namespace App\Entity;

use PiedWeb\ReservationBundle\Entity\Basket as BaseBasket;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="PiedWeb\ReservationBundle\Repository\BasketRepository")
 */
class Basket extends BaseBasket
{
}
