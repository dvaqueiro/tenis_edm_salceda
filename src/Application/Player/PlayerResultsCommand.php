<?php

namespace Application\Player;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class PlayerResultsCommand
{
    private $jugadorId;
    private $idLiga;

    function __construct($jugadorId, $idLiga = null)
    {
        $this->jugadorId = $jugadorId;
        $this->idLiga = $idLiga;
    }

    function getJugadorId()
    {
        return $this->jugadorId;
    }

    function getIdLiga()
    {
        return $this->idLiga;
    }
}

