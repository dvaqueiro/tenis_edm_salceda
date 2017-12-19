<?php

namespace Domain\Model\Resultado;

use Carbon\Carbon;
use Ddd\Domain\DomainEvent;

/**
 *
 * @author dvaqueiro
 */
class NewResultWasCreatedEvent implements DomainEvent
{
    private $resultadoId;
    private $occurredOn;

    function __construct($resultadoId)
    {
        $this->occurredOn = Carbon::now('UTC')->getTimestamp();
        $this->resultadoId = $resultadoId;
    }

    public function occurredOn()
    {
        return $this->occurredOn;
    }

    function getResultadoId()
    {
        return $this->resultadoId;
    }

}