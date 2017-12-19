<?php

namespace Domain\Model\Resultado;

/**
 * Null implementation of TennisSet
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class NullSet implements TennisSet
{
    function getJuegosVisitante()
    {
        return 0;
    }

    function getJuegosLocal()
    {
        return 0;
    }

    function getGanadorSet()
    {
        return TennisSet::_SIN_GANADOR_;
    }

    function getJuegos($cualJugador)
    {
        return null;
    }
}