<?php

namespace PiedWeb\ReservationBundle\Entity;

use PiedWeb\ReservationBundle\Entity\PageTrait as PageReservationTrait;
use PiedWeb\CMSBundle\Entity\IdTrait;
use PiedWeb\CMSBundle\Entity\PageTrait;
use PiedWeb\CMSBundle\Entity\PageExtendedTrait;
use PiedWeb\CMSBundle\Entity\PageImageTrait;
use PiedWeb\CMSBundle\Entity\TranslatableTrait;
use PiedWeb\CMSBundle\Entity\PageInterface;
use Doctrine\ORM\Mapping as ORM;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
class Page implements TranslatableInterface, PageInterface
{
    use IdTrait, PageTrait, PageExtendedTrait, PageImageTrait, TranslatableTrait, PageReservationTrait;

    public function __construct()
    {
        $this->__construct_page();
        $this->__construct_extended();
        $this->__construct_image();
        $this->__construct_product();
    }
}
