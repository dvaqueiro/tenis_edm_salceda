<?php

namespace Application\CourtBooking;

use Domain\Model\Reserva;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class AddReservaCommand
{
    private $reserva;

    function __construct(Reserva $reserva)
    {
        $this->reserva = $reserva;
    }

    function getReserva()
    {
        return $this->reserva;
    }
}