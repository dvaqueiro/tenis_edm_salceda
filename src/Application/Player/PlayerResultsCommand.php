<?php

namespace Application\Player;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class PlayerResultsCommand
{
    private $jugadorId;

    function __construct($jugadorId)
    {
        $this->jugadorId = $jugadorId;
    }

    function getJugadorId()
    {
        return $this->jugadorId;
    }
}