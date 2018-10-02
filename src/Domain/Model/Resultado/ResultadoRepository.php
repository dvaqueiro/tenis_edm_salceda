<?php

namespace Domain\Model\Resultado;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
interface ResultadoRepository
{

    public function add(Resultado $resultado);

    public function findByLigaAndJugador($idLiga, $idJugador);

    public function findByDivision($idDivision);

    public function remove($resultadoId);

    public function find($resultadoId);
}
