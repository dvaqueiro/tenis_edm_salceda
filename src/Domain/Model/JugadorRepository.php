<?php

namespace Domain\Model;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
interface JugadorRepository
{

    public function update(Jugador $jugador);

    public function findById($id);

    public function findByDni($dni);

    public function add(Jugador $newJugador);

    function findByDivision($idDivision);

}