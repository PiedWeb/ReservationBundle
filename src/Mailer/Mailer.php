<?php

namespace PiedWeb\ReservationBundle\Mailer;

use PiedWeb\ReservationBundle\Entity\OrderInterface;

class Mailer extends BaseMailer
{
    public function sendPaidWithSuccessMessage(OrderInterface $order)
    {
        $rendered = $this->templating->render('@PiedWebReservation/reservation/email_paid_success.html.twig', [
                    'order' => $order,
                    'fromEmail' => $this->fromEmail,
                    'fromName' => $this->fromName,
                ]);
        $this->sendEmailMessage($rendered, $this->fromEmail, (string) $order->getUser()->getEmail());
    }

    public function sendReservationValidationMessage(OrderInterface $order)
    {
        $rendered = $this->templating->render('@PiedWebReservation/reservation/email_success.html.twig', [
                    'order' => $order,
                    'fromEmail' => $this->fromEmail,
                    'fromName' => $this->fromName,
                ]);
        $this->sendEmailMessage($rendered, $this->fromEmail, (string) $order->getUser()->getEmail());
    }
}
