<?php

namespace PiedWeb\ReservationBundle\Controller;

use PiedWeb\ReservationBundle\Entity\ProductInterface as Product;
use PiedWeb\ReservationBundle\Entity\BasketInterface as Basket;
use PiedWeb\CMSBundle\Entity\UserInterface as User;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

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

    protected function showRegister($profileEdit = false): Response
    {
        // It may possible an User bypass detailled registration and we need to force hom
        // to do it, else, we will not be able to do step 4.
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $this->getUser();
        } else {
            $userClass = $this->container->getParameter('app.entity_user');
            $user = new $userClass();
            $user->setEnabled(true);
        }

        $form = $this->createFormBuilder($user)
            ->add('lastname', TextType::class)
            ->add('firstname', TextType::class)
            ->add('dateOfBirth', DateType::class, ['widget' => 'single_text', 'required' => false])
            ->add('phone', TelType::class)
            ->add('city', TextType::class)
        ;

        if (!$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') || $profileEdit) {
            $form
                ->add('email', EmailType::class)
                ->add('plainPassword', PasswordType::class, [
                    'always_empty' => false,
                    'required' => $profileEdit ? false : true,
                    'help' => $profileEdit ? 'Laissez vide le garder tel quel' : null,
                ])
            ;
        }

        $form = $form->getForm();

        $form->handleRequest($this->container->get('request_stack')->getCurrentRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            // Register
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();

            // Logged in
            if (!$profileEdit) {
                $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                $this->get('security.token_storage')->setToken($token);

                $response = $this->redirectToRoute('piedweb_reservation_step_4', ['previous' => 3]);

                // Transform basket from visitor to this user
                if (!$profileEdit) {
                    $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $this->requestStack->getCurrentRequest(), $response));
                }

                return $response;
            }
        }

        return $this->render('@PiedWebReservation/'.($profileEdit ? 'user/profile_edit' : 'reservation/reservation').'.html.twig', [
            'step' => 3,
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
