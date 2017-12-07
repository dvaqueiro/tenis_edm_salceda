<?php

namespace Application\CourtBooking;

use Carbon\Carbon;
use Ddd\Domain\DomainEvent;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class CourtBookingWasConfirmedEvent implements DomainEvent
{
    private $occurredOn;
    private $reservaId;

    function __construct($reservaId)
    {
        $this->occurredOn = Carbon::now('UTC')->getTimestamp();
        $this->reservaId = $reservaId;
    }

    public function occurredOn()
    {
        return $this->occurredOn;
    }

    function getReservaId()
    {
        return $this->reservaId;
    }


}