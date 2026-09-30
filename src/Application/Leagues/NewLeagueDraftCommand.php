<?php

namespace Application\Leagues;

/**
 * Datos necesarios para preparar las tablas de una nueva liga a partir
 * de la clasificación de una liga anterior.
 */
class NewLeagueDraftCommand
{
    private $idLigaBase;
    private $puntosGanador;
    private $puntosPerdedor;
    private $order;
    private $limiteLigas;

    function __construct($idLigaBase, $puntosGanador, $puntosPerdedor, $order, $limiteLigas = 20)
    {
        $this->idLigaBase = $idLigaBase;
        $this->puntosGanador = $puntosGanador;
        $this->puntosPerdedor = $puntosPerdedor;
        $this->order = $order;
        $this->limiteLigas = $limiteLigas;
    }

    function getIdLigaBase()
    {
        return $this->idLigaBase;
    }

    function getPuntosGanador()
    {
        return $this->puntosGanador;
    }

    function getPuntosPerdedor()
    {
        return $this->puntosPerdedor;
    }

    function getOrder()
    {
        return $this->order;
    }

    function getLimiteLigas()
    {
        return $this->limiteLigas;
    }
}
