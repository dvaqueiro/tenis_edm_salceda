<?php

namespace Application;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class HorasLibresReservaCommand
{
    private $fecha;
    private $pista;

    function __construct($pista, $fecha)
    {
        $this->pista = $pista;
        $this->fecha = $fecha;
    }

    function getFecha()
    {
        return $this->fecha;
    }

    function getPista()
    {
        return $this->pista;
    }
}