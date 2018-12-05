<?php

namespace PiedWeb\ReservationBundle\Controller;

use PiedWeb\ReservationBundle\Entity\ProductInterface as Product;
use PiedWeb\ReservationBundle\Entity\BasketInterface as Basket;
use PiedWeb\CMSBundle\Entity\UserInterface as User;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait HelperTrait
{
    protected $newCookie;

    protected $cookieBasket = 'piedweb_basket';

    /**
     * @throw NotFoundHttpException
     *
     * @return ProductInterface
     */
    protected function getProduct(int $id): Product
    {
        $product = $this->getDoctrine()->getRepository($this->container->getParameter('app.entity_product'))->findOneById($id);
        if (null === $product) {
            throw new NotFoundHttpException();
        }

        return $product;
    }

    protected function getBasket(int $id)
    {
        if (null !== $id) {
            $basket = $this->em->getRepository($this->container->getParameter('app.entity_basket'))->findOneById($id);
            if (null === $basket) {
                return null; //throw new NotFoundHttpException();
            }

            return $basket;
        }
    }

    /**
     * Return the basket for the current user
     *      Create it if not exist.
     */
    protected function getBasketForCurrentUser()
    {
        $basketClass = $this->container->getParameter('app.entity_basket');
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $basket = $this->getUser()->getBasket();
            if (null === $basket) {
                $basket = new $basketClass();
                $this->getUser()->setBasket($basket);
                $basket->setUser($this->getUser()); // required ???
                $this->em->persist($basket);
                $this->em->flush();
            }
        } else {
            $basket = $this->getBasketFromCookie();
            if (null === $basket) {
                $basket = new $basketClass();
                $this->em->persist($basket);
                $this->em->flush();
                $this->newCookie = new Cookie($this->cookieBasket, $basket->getId(), new \Datetime('+ 6 months'));
            }
        }

        return $basket;
    }

    protected function getBasketFromCookie()
    {
        if ($basketId = $this->getBasketIdFromCookie()) {
            return $this->getBasket($basketId);
        }
    }

    protected function getBasketIdFromCookie()
    {
        return $this->requestStack->getCurrentRequest()->cookies->get($this->cookieBasket);
    }

    /*
     * @throw NotFoundHttpException
     */
    protected function getOrder(int $id)
    {
        $order = $this->getDoctrine()->getRepository($this->container->getParameter('app.entity_order'))->findOneById($id);
        if (null === $order) {
            throw new NotFoundHttpException();
        }

        return $order;
    }
}
