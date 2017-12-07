<?php

namespace Application\CourtBooking;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class ConfirmBookingCommand
{
    private $idReserva;
    private $token;

    public function __construct($token, $idReserva)
    {
        $this->token = $token;
        $this->idReserva = $idReserva;
    }

    function getIdReserva()
    {
        return $this->idReserva;
    }

    function getToken()
    {
        return $this->token;
    }

}