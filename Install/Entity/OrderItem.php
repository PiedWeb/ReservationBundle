<?php

namespace App\Entity;

use PiedWeb\ReservationBundle\Entity\OrderItem as BaseOrderItem;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="PiedWeb\ReservationBundle\Repository\OrderItemRepository")
 */
class OrderItem extends BaseOrderItem
{
}
