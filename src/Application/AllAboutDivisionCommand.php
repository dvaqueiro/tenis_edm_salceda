<?php

namespace Application;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class AllAboutDivisionCommand
{
    private $idDivision;
    private $order;
    private $puntosPerdedor;
    private $puntosGanador;
    private $idLiga;

    function __construct($idLiga, $idDivision, $puntosGanador, $puntosPerdedor, $order)
    {

        $this->puntosGanador = $puntosGanador;
        $this->puntosPerdedor = $puntosPerdedor;
        $this->idLiga = $idLiga;
        $this->order = $order;
        $this->idDivision = $idDivision;
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

    function getIdDivision()
    {
        return $this->idDivision;
    }
}