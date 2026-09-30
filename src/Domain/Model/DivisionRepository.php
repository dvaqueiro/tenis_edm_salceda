<?php

namespace Domain\Model;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
interface DivisionRepository
{

    public function findById($divisionId);

    public function findByLigaAndJugador($idLiga, $idJugador);

    /**
     *
     * @param int $idLiga
     * @return Division[]
     */
    public function findByLiga($idLiga);

    /**
     *
     * @param int $idLiga
     * @param string $nombre
     * @param int $categoria
     * @return int id de la nueva división
     */
    public function add($idLiga, $nombre, $categoria);

    /**
     *
     * @param int $idDivision
     * @param int $idJugador
     */
    public function addJugador($idDivision, $idJugador);
}