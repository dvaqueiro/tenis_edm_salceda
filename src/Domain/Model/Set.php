<?php

namespace Domain\Model;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class Set
{
    const _SIN_GANADOR_ = 0;
    const _LOCAL_ = 1;
    const _VISITANTE_ = 2;
    
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
            return self::_LOCAL_;
        }

        if($this->juegosLocal == 7 && ($this->juegosVisitante == 5 || $this->juegosVisitante == 6)) {
            return self::_LOCAL_;
        }

        if($this->juegosVisitante == 6 && $this->juegosLocal < 5) {
            return self::_VISITANTE_;
        }

        if($this->juegosVisitante == 7 && ($this->juegosLocal == 5 || $this->juegosLocal == 6)) {
            return self::_VISITANTE_;
        }

        return self::_SIN_GANADOR_;
    }

    function getJuegos($cualJugador)
    {
        if ($cualJugador == self::_LOCAL_) {
            return $this->juegosLocal;
        }

        if ($cualJugador == self::_VISITANTE_) {
            return $this->juegosVisitante;
        }

        return 0;
    }
}