<?php

namespace Domain\Model\Resultado;

/**
 *
 * @author dvaqueiro
 */
interface TennisSet
{
    const _SIN_GANADOR_ = 0;
    const _LOCAL_ = 1;
    const _VISITANTE_ = 2;

    function getJuegosVisitante();

    function getJuegosLocal();

    function getGanadorSet();

    function getJuegos($cualJugador);
}