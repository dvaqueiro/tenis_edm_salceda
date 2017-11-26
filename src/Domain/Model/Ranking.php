<?php

namespace Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class Ranking
{
    private $puntosPorCategoria;
    private $orderBy;
    private $puntosPerdedor;
    private $puntosGanador;
    private $ligas;

    /**
     *
     * @var ArrayCollection
     */
    private $agregados;

    function __construct($ligas, $puntosPorCategoria, $puntosGanador = 3, $puntosPerdedor = 1, $orderBy = null)
    {
        $this->ligas = $ligas;
        $this->puntosPorCategoria = $puntosPorCategoria;
        $this->agregados = new ArrayCollection();
        $this->puntosGanador = $puntosGanador;
        $this->puntosPerdedor = $puntosPerdedor;
        $this->orderBy = $orderBy;
        $this->construirRankings();
    }

    private function construirRankings()
    {
        $this->procesarLigas();
    }

    private function procesarLigas()
    {
        foreach ($this->ligas as $liga) {
            /* @var $liga Liga */
            $this->procesarDivisiones($liga);
        }
    }

    private function procesarDivisiones($liga)
    {
        foreach ($liga->getDivisiones() as $division) {
            $this->procesarResultados($division);
        }
    }

    private function procesarResultados(Division $division)
    {
        /* @var $division Division */
        $categoria = $division->getCategoria();
        $puntosPorCategoria = (isset($this->puntosPorCategoria[$categoria])) ? $this->puntosPorCategoria[$categoria] : 0;
        foreach ($division->getResultados() as $resultado) {
            /* @var $resultado Resultado */
            if($resultado->getIdGanador() == null) continue;
            $this->initAgregado($resultado->getIdGanador(), $resultado->getNombreGanador());
            $this->initAgregado($resultado->getIdPerdedor(), $resultado->getNombrePerdedor());

            $this->incrementValue($resultado->getIdGanador(), 'puntos', $this->puntosGanador);
            $this->incrementValue($resultado->getIdPerdedor(), 'puntos', $this->puntosPerdedor);

            $this->incrementValue($resultado->getIdGanador(), 'jugados', 1);
            $this->incrementValue($resultado->getIdPerdedor(), 'jugados', 1);

            $this->incrementValue($resultado->getIdGanador(), 'victorias', 1);

            $this->incrementarPuntosPorCategoria($resultado->getIdGanador(), $division, 'puntos', $puntosPorCategoria);
            $this->incrementarPuntosPorCategoria($resultado->getIdPerdedor(), $division, 'puntos', $puntosPorCategoria);
        }
    }

    private function initAgregado($key, $nombre)
    {
        if(!$this->agregados->containsKey($key)) {
            $this->agregados->set($key, [
                'nombre' => $nombre,
                'puntos' => 0,
                'victorias' => 0,
                'jugados' => 0,
            ]);
        }
    }

    private function incrementValue($idJugador, $name, $value)
    {
        $datos = $this->agregados->get($idJugador);
        $datos[$name] += $value;
        $this->agregados->set($idJugador, $datos);
    }

    private function incrementarPuntosPorCategoria($idJugador, Division $division, $name, $value)
    {
        $datos = $this->agregados->get($idJugador);
        if(isset($datos[$division->getIdDivision()])) return;
        $this->incrementValue($idJugador, $name, $value);
        $datos = $this->agregados->get($idJugador);
        $datos[$division->getIdDivision()] = $value;
        $this->agregados->set($idJugador, $datos);
    }

    public function getAgregados()
    {
        return $this->agregados->matching(
            Criteria::create()->orderBy($this->orderBy));
    }
}