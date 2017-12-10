<?php

namespace Application\Player;

use Carbon\Carbon;
use Ddd\Domain\DomainEvent;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class PlayerWasRegisterEvent implements DomainEvent
{
    private $occurredOn;
    private $jugadorId;

    function __construct($jugadorId)
    {
        $this->occurredOn = Carbon::now('UTC')->getTimestamp();
        $this->jugadorId = $jugadorId;
    }

    public function occurredOn()
    {
        return $this->occurredOn;
    }

    function getJugadorId()
    {
        return $this->jugadorId;
    }
}