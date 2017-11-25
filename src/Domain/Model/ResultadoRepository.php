<?php

namespace Domain\Model;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
interface ResultadoRepository
{

    public function findByDivision($idDivision);
}