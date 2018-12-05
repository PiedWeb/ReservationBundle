<?php

namespace PiedWeb\ReservationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use PiedWeb\CMSBundle\Entity\UserInterface as User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security as aSecurity;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;

class ManageController extends AbstractController
{
    use HelperTrait;

    protected $requestStack;
    protected $em;

    public function __construct(EntityManagerInterface $em, RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->em = $em;
    }

    /**
     * @aSecurity("has_role('ROLE_USER')")
     */
    public function showInvoice(int $id)
    {
        $order = $this->getOrder($id);

        if (null === $order || (
              !$this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')
               || $this->getUser() != $order->getUser()
            )) {
            throw $this->createNotFoundException();
        }

        $invoiceItems = [];
        $total = 0;
        $total_ht = 0;
        $total_vat = 0;
        foreach ($order->getOrderItems() as $item) {
            $key = $item->getProduct()->getId().'-'.$item->getPrice();
            if (!isset($invoiceItems[$key])) {
                $invoiceItems[$key] = [
                    'name' => $item->getProduct()->getName(),
                    'quantity' => 1,
                    'unit_price_ht' => $item->getPriceHt(),
                    'unit_price' => $item->getPrice(),
                    'vat' => $item->getVat(),
                    'price' => $item->getPrice(),
                ];
            } else {
                ++$invoiceItems[$key]['quantity'];
                $invoiceItems[$key]['price'] = $invoiceItems[$key]['price'] + $item->getPrice();
            }
            $total = $total + $item->getPrice();
            $total_ht = $total_ht + $item->getPriceHt();
            $total_vat = $total_vat + ($item->getPriceHt() * ($item->getVat() / 100));
        }

        $data = [
            'order' => $order,
            'total' => $total,
            'total_ht' => $total_ht,
            'total_vat' => $total_vat,
            'invoiceItems' => $invoiceItems,
        ];

        return $this->render('@PiedWebReservation/reservation/invoice.html.twig', $data);
    }

    public function showBasket(): Response
    {
        $user = $this->getUser();
        $basket = $user->getBasket();

        if ($basket->getBasketItems()->isEmpty()) {
            return $this->render('@PiedWebReservation/reservation/no_reservation.html.twig');
        }

        foreach ($basket->getBasketItems() as $basketItem) {
            if (!$this->doesThisBasketItemCompleted($basketItem)) {
                return $this->redirectToRoute('piedweb_reservation_step_4');
            }
        }

        $form = $this->createFormBuilder()
            ->add('creditcard', SubmitType::class)
            ->add('espece', SubmitType::class)
            ->getForm();

        $form->handleRequest($this->container->get('request_stack')->getCurrentRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            $order = $this->createOrder($form);
            if (false !== $order) {
                return $this->redirectToRoute('piedweb_reservation_step_6', ['id' => $order->getId()]);
            }
        }

        $data = ['step' => 5, 'form' => $form->createView(), 'basket' => $basket];

        return $this->render('@PiedWebReservation/reservation/reservation.html.twig', $data);
    }
}
