<?php

namespace PiedWeb\ReservationBundle\PaymentMethod;

class PaypalCheckoutExpress extends PaymentMethod
{
    /**
     * @var int
     */
    protected $id = 1;

    /**
     * @var string
     */
    protected $humanId = 'paypal_checkout_express';

    /**
     * @var string
     */
    protected $redirectRoute = 'piedweb_reservation_payment_paypal_checkout_express';
}
