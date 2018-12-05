<?php

namespace PiedWeb\ReservationBundle\EventListener;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RegistrationListener implements EventSubscriberInterface
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

    public function onRegistrationCompleted(FilterUserResponseEvent $event)
    {
        $user = $event->getUser();

        $basket = $this->getBasketFromCookie();
        if (null !== $basket) {
            $basket->setUser($user);
            $this->em->flush();
            $event->getResponse()->headers->clearCookie($this->cookieBasket);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            FOSUserEvents::REGISTRATION_COMPLETED => 'onRegistrationCompleted',
        ];
    }
}
