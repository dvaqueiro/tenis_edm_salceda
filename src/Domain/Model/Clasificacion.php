<?php

namespace Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class Clasificacion
{
    private $participantes;
    private $orderBy;
    private $puntosPerdedor;
    private $puntosGanador;
    private $resultados;

    /**
     *
     * @var ArrayCollection
     */
    private $agregados;

    function __construct($participantes, $resultados, $puntosGanador = 3, $puntosPerdedor = 1, $orderBy = null)
    {
        $this->participantes = $participantes;
        $this->resultados = $resultados;
        $this->agregados = new ArrayCollection();
        $this->puntosGanador = $puntosGanador;
        $this->puntosPerdedor = $puntosPerdedor;
        $this->orderBy = $orderBy;
        $this->setParticipantes();
        $this->construirClasificacion();
    }

    private function setParticipantes()
    {
        foreach ($this->participantes as $participante) {
            /* @var $participante Jugador */
            $this->initAgregado($participante->getId(), $participante->getNombre());
        }
    }

    private function construirClasificacion()
    {
        foreach ($this->resultados as $resultado) {
            if($resultado->getIdGanador() == null) continue;

            $this->incrementValue($resultado->getIdGanador(), 'puntos', $this->puntosGanador);
            $this->incrementValue($resultado->getIdPerdedor(), 'puntos', $this->puntosPerdedor);

            $this->incrementValue($resultado->getIdGanador(), 'partidos', 1);
            $this->incrementValue($resultado->getIdPerdedor(), 'partidos', 1);

            $this->incrementValue($resultado->getIdGanador(), 'win', 1);
            $this->incrementValue($resultado->getIdPerdedor(), 'lost', 1);

            $this->incrementValue($resultado->getIdGanador(), 'difSets', $resultado->getDiferenciaSetsGanador());
            $this->incrementValue($resultado->getIdPerdedor(), 'difSets', $resultado->getDiferenciaSetsPerdedor());

            $this->incrementValue($resultado->getIdGanador(), 'difJuegos', $resultado->getDiferenciaJuegosGanador());
            $this->incrementValue($resultado->getIdPerdedor(), 'difJuegos', $resultado->getDiferenciaJuegosPerdedor());
        }
    }

    private function initAgregado($key, $nombre)
    {
        if(!$this->agregados->containsKey($key)) {
            $this->agregados->set($key, [
                'nombre' => $nombre,
                'puntos' => 0,
                'partidos' => 0,
                'difSets' => 0,
                'difJuegos' => 0,
                'win' => 0,
                'lost' => 0,
            ]);
        }
    }

    private function incrementValue($idJugador, $name, $value)
    {
        $datos = $this->agregados->get($idJugador);
        $datos[$name] += $value;
        $this->agregados->set($idJugador, $datos);
    }

    public function getAgregados()
    {
        return $this->agregados->matching(
            Criteria::create()->orderBy($this->orderBy));
    }
}