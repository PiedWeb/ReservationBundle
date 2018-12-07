<?php

namespace PiedWeb\ReservationBundle\PaymentMethod;

abstract class PaymentMethod
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $humanId;

    /**
     * @var string
     */
    protected $redirectRoute;

    public function getId()
    {
        return $this->id;
    }

    public function getHumanId()
    {
        return $this->humanId;
    }

    public function getRedirectRoute()
    {
        return $this->redirectRoute;
    }
}
