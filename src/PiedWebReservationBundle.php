<?php

namespace PiedWeb\ReservationBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use PiedWeb\ReservationBundle\DependencyInjection\PiedWebReservationExtension;

class PiedWebReservationBundle extends Bundle
{
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new PiedWebReservationExtension();
        }

        return $this->extension;
    }
}
