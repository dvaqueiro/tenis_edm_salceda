<?php

namespace Application\CourtBooking;

/**
 *
 * @author <dvaqueiro at boardfy dot com>
 */
class SendMailToBookingConfirmationCommand
{
    private $reservaId;

    function __construct($reservaId)
    {
        $this->reservaId = $reservaId;
    }

    function getReservaId()
    {
        return $this->reservaId;
    }

}