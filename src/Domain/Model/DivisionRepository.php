<?php

namespace Domain\Model;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
interface DivisionRepository
{

    public function findByLigaAndJugador($idLiga, $idJugador);

    /**
     *
     * @param int $idLiga
     * @return Division[]
     */
    public function findByLiga($idLiga);
}