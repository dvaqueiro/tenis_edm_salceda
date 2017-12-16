<?php

namespace Domain\Model;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
interface ResultadoRepository
{

    public function findByLigaAndJugador($idLiga, $idJugador);

    public function findByDivision($idDivision);
}