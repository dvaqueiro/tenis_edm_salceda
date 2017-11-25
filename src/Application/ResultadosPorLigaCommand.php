<?php

namespace Application;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class ResultadosPorLigaCommand
{
    private $idLiga;

    function __construct($idLiga)
    {
        $this->idLiga = $idLiga;
    }

    function getIdLiga()
    {
        return $this->idLiga;
    }

}