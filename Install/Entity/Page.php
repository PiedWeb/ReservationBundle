<?php

namespace App\Entity;

use PiedWeb\ReservationBundle\Entity\Page as BasePage;
use Doctrine\ORM\Mapping as ORM;
use PiedWeb\CMSBundle\Repository\PageRepository;

/**
 * @ORM\Entity(repositoryClass=PageRepository::class)
 */
class Page extends BasePage
{
}
