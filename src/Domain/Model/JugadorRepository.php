<?php

namespace Domain\Model;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
interface JugadorRepository
{

    public function findRivales($idLiga, $idJugador);

    public function update(Jugador $jugador);

    /**
     *
     * @param int $id
     * @return Jugador
     */
    public function findById($id);

    public function findByDni($dni);

    public function add(Jugador $newJugador);

    public function delete($jugadorId);

    function findByDivision($idDivision);

    function findAll();

    function findAllWithRoles($roles);

}