<?php

namespace App\Entity;

use PiedWeb\ReservationBundle\Entity\BasketItem as BaseBasketItem;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="PiedWeb\ReservationBundle\Repository\BasketItemRepository")
 */
class BasketItem extends BaseBasketItem
{
}
