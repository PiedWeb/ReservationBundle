<?php

// Example class, should not be used !

namespace PiedWeb\ReservationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use PiedWeb\CMSBundle\Entity\UserTrait;
use PiedWeb\CMSBundle\Entity\UserInterface;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity("email",
 *     message="user.email.already_used"
 * )
 */
class User extends BaseUser implements UserInterface
{
    use UserTrait, UserExtendedForReservationTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
}
