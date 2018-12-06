<?php

namespace PiedWeb\ReservationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use PiedWeb\CMSBundle\Entity\UserInterface as User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security as aSecurity;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

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

    /**
     * @aSecurity("has_role('ROLE_USER')")
     */
    public function showOrders(): Response
    {
        $user = $this->getUser();
        $orders = $user->getOrders();

        $data = [
            'orders' => $orders,
            'user' => $user,
        ];
        return $this->render('@PiedWebReservation/reservation/orders.html.twig', $data);
    }
}
