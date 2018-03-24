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
    private $confirmado;

    public function __construct($token, $idReserva, $confirmado = \Domain\Model\Reserva::_APROBADO_)
    {
        $this->token = $token;
        $this->idReserva = $idReserva;
        $this->confirmado = $confirmado;
    }

    function getIdReserva()
    {
        return $this->idReserva;
    }

    function getToken()
    {
        return $this->token;
    }

    function getConfirmado()
    {
        return $this->confirmado;
    }

}