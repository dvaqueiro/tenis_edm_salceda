<?php

namespace Application;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class RankingCommand
{
    private $limit;
    private $puntosPorCategoria;
    private $puntosGanador;
    private $puntosPerdedor;
    private $orderBy;

    function __construct($limit, $puntosPorCategoria, $puntosGanador, $puntosPerdedor, $orderBy)
    {
        $this->limit = $limit;
        $this->orderBy = $orderBy;
        $this->puntosPerdedor = $puntosPerdedor;
        $this->puntosGanador = $puntosGanador;
        $this->puntosPorCategoria = $puntosPorCategoria;
    }

    function getLimit()
    {
        return $this->limit;
    }

    function getPuntosPorCategoria()
    {
        return $this->puntosPorCategoria;
    }

    function getPuntosGanador()
    {
        return $this->puntosGanador;
    }

    function getPuntosPerdedor()
    {
        return $this->puntosPerdedor;
    }

    function getOrderBy()
    {
        return $this->orderBy;
    }

}