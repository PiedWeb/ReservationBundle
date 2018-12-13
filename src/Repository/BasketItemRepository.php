<?php

namespace PiedWeb\ReservationBundle\Repository;

use PiedWeb\ReservationBundle\Entity\BasketItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method BasketItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method BasketItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method BasketItem[]    findAll()
 * @method BasketItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BasketItemRepository extends ServiceEntityRepository
{
}
