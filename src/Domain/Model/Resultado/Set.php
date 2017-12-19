<?php

namespace Domain\Model\Resultado;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class Set implements TennisSet
{
    private $juegosVisitante;
    private $juegosLocal;
    private $ganador;

    function __construct($juegosLocal, $juegosVisitante)
    {
        $this->juegosLocal = $juegosLocal;
        $this->juegosVisitante = $juegosVisitante;
        $this->ganador = $this->calcularGanador();
    }

    function getJuegosVisitante()
    {
        return $this->juegosVisitante;
    }

    function getJuegosLocal()
    {
        return $this->juegosLocal;
    }

    function getGanadorSet()
    {
        return $this->ganador;
    }

    private function calcularGanador()
    {
        if($this->juegosLocal == 6 && $this->juegosVisitante < 5) {
            return TennisSet::_LOCAL_;
        }

        if($this->juegosLocal == 7 && ($this->juegosVisitante == 5 || $this->juegosVisitante == 6)) {
            return TennisSet::_LOCAL_;
        }

        if($this->juegosVisitante == 6 && $this->juegosLocal < 5) {
            return TennisSet::_VISITANTE_;
        }

        if($this->juegosVisitante == 7 && ($this->juegosLocal == 5 || $this->juegosLocal == 6)) {
            return TennisSet::_VISITANTE_;
        }

        return TennisSet::_SIN_GANADOR_;
    }

    function getJuegos($cualJugador)
    {
        if ($cualJugador == TennisSet::_LOCAL_) {
            return $this->juegosLocal;
        }

        if ($cualJugador == TennisSet::_VISITANTE_) {
            return $this->juegosVisitante;
        }

        return 0;
    }
}