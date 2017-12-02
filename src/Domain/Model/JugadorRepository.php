<?php

namespace Domain\Model;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
interface JugadorRepository
{

    public function findByDni($dni);

    public function add(Jugador $newJugador);

    function findByDivision($idDivision);

}