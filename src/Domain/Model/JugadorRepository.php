<?php

namespace Domain\Model;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
interface JugadorRepository
{

    public function add(Jugador $newJugador);

    function findByDivision($idDivision);

}