<?php

namespace App\Entity;

use PiedWeb\ReservationBundle\Entity\Product as BaseProduct;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="PiedWeb\ReservationBundle\Repository\ProductRepository")
 */
class Product extends BaseProduct
{
}
