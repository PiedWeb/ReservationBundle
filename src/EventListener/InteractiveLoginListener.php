<?php

namespace PiedWeb\ReservationBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InteractiveLoginListener
{
    use \PiedWeb\ReservationBundle\Controller\HelperTrait;

    private $em;
    private $requestStack;
    private $container;

    public function __construct(ContainerInterface $container, EntityManagerInterface $em, RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * We set the basket created being a non authenticated to the user.
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $basket = $this->getBasketFromCookie();
        $user = $event->getAuthenticationToken()->getUser();

        if (null !== $basket) {
            if (null !== $user->getBasket()) {
                foreach ($basket->getBasketItems() as $basketItem) {
                    // avoid to add a product for him if it's ever added
                    // todo add the same check for product ever paid (in orderItem)
                    if (true !== $user->getBasket()->issetItemForMe($basketItem->getProduct()->getId())) {
                        $basketItem->setBasket($user->getBasket());
                    } else {
                        $this->em->remove($basketItem);
                    }
                }
                $this->em->remove($basket);
            } else {
                $basket->setUser($user);
            }
            $this->em->flush();
        }

        // not absolutely needed because basket is deleted on first login :
        // todo: clear cookie, view LoginListener.php
    }
}
