<?php

namespace PiedWeb\ReservationBundle\PaymentMethod;

class Cash extends PaymentMethod
{
    /**
     * @var int
     */
    protected $id = 2;

    /**
     * @var string
     */
    protected $humanId = 'cash';

    /**
     * @var string
     */
    protected $redirectRoute = 'piedweb_reservation_step_6';
}
