<?php

namespace Application;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class JugadoresPorLigaCommand
{
    private $ligaId;

    function __construct($ligaId)
    {
        $this->ligaId = $ligaId;
    }

    public function getLigaId()
    {
        return $this->ligaId;
    }

}