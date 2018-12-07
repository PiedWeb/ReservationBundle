<?php

namespace PiedWeb\ReservationBundle\PaymentMethod;

class Free extends PaymentMethod
{
    /**
     * @var int
     */
    protected $id = 3;

    /**
     * @var string
     */
    protected $humanId = 'free';

    /**
     * @var string
     */
    protected $redirectRoute = 'piedweb_reservation_step_6';
}
