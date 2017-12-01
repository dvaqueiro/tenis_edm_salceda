<?php

namespace Application;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class RegisterJugadorCommand
{
    /**
     * @var \Domain\Model\Jugador
     */
    private $jugador;

    function __construct(\Domain\Model\Jugador $jugador)
    {

        $this->jugador = $jugador;
    }

    function getJugador(): \Domain\Model\Jugador
    {
        return $this->jugador;
    }

}