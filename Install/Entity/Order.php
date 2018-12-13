<?php

namespace App\Entity;

use PiedWeb\ReservationBundle\Entity\Order as BaseOrder;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="PiedWeb\ReservationBundle\Repository\OrderRepository")
 * @ORM\Table(name="`Order`")
 */
class Order extends BaseOrder
{
}
