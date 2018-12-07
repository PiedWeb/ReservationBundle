<?php

namespace PiedWeb\ReservationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Payum\Core\Request\GetHumanStatus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use PiedWeb\ReservationBundle\Mailer\Mailer;

class PaymentController extends AbstractController
{
    use HelperTrait;

    protected $mailer;

    public function __construct(
        Mailer $mailer
    ) {
        $this->mailer = $mailer;
    }

    /**
    public function preparePaypalProCeckout(int $id)
    {
        $order = $this->getOrder($id);


        $gatewayName = 'TODO';

        $storage = $this->get('payum')->getStorage('Payum\Core\Model\Payment');

        $payment = $storage->create();
        $payment->setNumber($order->getId());
        $payment->setCurrencyCode('EUR');
        $payment->setTotalAmount(intval(round($order->getPrice()*100))); // 1.23 EUR.
        $payment->setDescription('A description');
        $payment->setClientId($order->getUser()->getId());
        $payment->setClientEmail($order->getUser()->getEmail());
        $storage->update($details);

        $captureToken = $this->get('payum')->getTokenFactory()->createCaptureToken(
            $gatewayName,
            $details,
            'piedweb_reservation_payment_done' // the route to redirect after capture;
        );

        return $this->redirect($captureToken->getTargetUrl());
    }
    /**/
    public function preparePaypalCheckoutButton(int $id)
    {
        $order = $this->getOrder($id);

        $gatewayName = 'paypal_checkout_button';

        $storage = $this->get('payum')->getStorage('Payum\Core\Model\Payment');

        $payment = $storage->create();
        $payment->setNumber($order->getId());
        $payment->setCurrencyCode('EUR');
        $payment->setTotalAmount(intval(round($order->getPrice() * 100))); // 1.23 EUR
        //$payment->setDescription('A description');
        //$payment->setClientId($order->getUser()->getId());
        //$payment->setClientEmail($order->getUser()->getEmail());
        $storage->update($payment);

        $captureToken = $this->get('payum')->getTokenFactory()->createCaptureToken(
            $gatewayName,
            $payment,
            'piedweb_reservation_payment_done' // the route to redirect after capture;
        );

        return $this->redirect($captureToken->getTargetUrl());
    }

    public function captureDoneAction(Request $request)
    {
        $token = $this->get('payum')->getHttpRequestVerifier()->verify($request);

        $identity = $token->getDetails();
        //$model = $this->get('payum')->getStorage($identity->getClass())->find($identity);
        $gateway = $this->get('payum')->getGateway($token->getGatewayName());

        // invalidate the token. The url could not be requested any more.
        $this->get('payum')->getHttpRequestVerifier()->invalidate($token);

        $gateway->execute($status = new GetHumanStatus($token));
        $details = $status->getFirstModel();
        /***************/

        $order = $this->getOrder($details->getNumber());

        if (GetHumanStatus::STATUS_CAPTURED === $status->getValue()) { // Reviens de paypal et c'est OK
            $order->setPaid(true);
            $order->setPaiementMethod(2);
            $order->setPaidAt(new \DateTime());
            $this->getDoctrine()->getManager()->flush();
            $this->mailer->sendPaidWithSuccessMessage($order);
            $this->addFlash('success', $this->container->get('translator')->trans('payment.success'));
        } else { // Reviens de paypal et c'est un pb
            $this->addFlash('danger', $this->container->get('translator')->trans('payment.error').' <a href="'.$this->container->get('router')->generate('piedweb_reservation_step_5_bis', ['id' => $order->getId()]).'">'.$this->container->get('translator')->trans('payment.retry').'</a>');
            // todo: log $status
        }

        return $this->redirectToRoute('piedweb_reservation_step_6', ['id' => $order->getId()]);
        // todo, remove redirect
    }
}
