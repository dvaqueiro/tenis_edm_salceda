<?php

namespace Application;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class ClasificacionPorLigaCommand
{
    private $order;
    private $puntosPerdedor;
    private $puntosGanador;
    private $idLiga;

    function __construct($idLiga, $puntosGanador, $puntosPerdedor, $order)
    {

        $this->puntosGanador = $puntosGanador;
        $this->puntosPerdedor = $puntosPerdedor;
        $this->idLiga = $idLiga;
        $this->order = $order;
    }

    function getOrder()
    {
        return $this->order;
    }

    function getPuntosPerdedor()
    {
        return $this->puntosPerdedor;
    }

    function getPuntosGanador()
    {
        return $this->puntosGanador;
    }

    function getIdLiga()
    {
        return $this->idLiga;
    }
}