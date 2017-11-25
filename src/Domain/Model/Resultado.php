<?php

namespace Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class Resultado
{
    const _SIN_GANADOR_ = 0;
    const _LOCAL_ = 1;
    const _VISITANTE_ = 2;
    
    private $nombreJugadorVisitante;
    private $nombreJugadorLocal;
    private $idJugadorVisitante;
    private $idJugadorLocal;
    private $idDivision;
    private $idResultado;
    private $sets;
    private $ganador;
    private $setGanadosLocal;
    private $setGanadosVisitante;
    private $nombreGanador;
    private $nombrePerdedor;
    private $perdedor;

    function __construct($idResultado, $idDivision, $idJugadorLocal, $idJugadorVisitante, $nombreJugadorLocal = null, $nombreJugadorVisitante = null)
    {
        $this->idResultado = $idResultado;
        $this->idDivision = $idDivision;
        $this->idJugadorLocal = $idJugadorLocal;
        $this->idJugadorVisitante = $idJugadorVisitante;
        $this->nombreJugadorLocal =  mb_convert_case($nombreJugadorLocal, MB_CASE_TITLE, "UTF-8");
        $this->nombreJugadorVisitante = mb_convert_case($nombreJugadorVisitante, MB_CASE_TITLE, "UTF-8");;
        $this->sets = new ArrayCollection();
        $this->setGanadosLocal = 0;
        $this->setGanadosVisitante = 0;
    }

    function addSet(Set $set)
    {
        $this->sets->add($set);
        $this->ganador = null;
        $this->setGanadosLocal = 0;
        $this->setGanadosVisitante = 0;
    }

    function getNombreJugadorVisitante()
    {
        return $this->nombreJugadorVisitante;
    }

    function getNombreJugadorLocal()
    {
        return $this->nombreJugadorLocal;
    }

    function getIdJugadorVisitante()
    {
        return $this->idJugadorVisitante;
    }

    function getIdJugadorLocal()
    {
        return $this->idJugadorLocal;
    }

    function getIdDivision()
    {
        return $this->idDivision;
    }

    function getIdResultado()
    {
        return $this->idResultado;
    }

    function getSet($numeroSet)
    {
        return $this->sets->get($numeroSet);
    }

    function getGanador()
    {
        if($this->ganador == null) {
            $this->calcularGanadorPartido();
        }
        return $this->ganador;
    }

    function getPerdedor()
    {
        if($this->perdedor == null) {
            $this->calcularGanadorPartido();
        }
        return $this->perdedor;
    }

    private function calcularGanadorPartido()
    {
        $setsParaGanar = intdiv($this->sets->count() , 2) + 1;
        $this->ganador = self::_SIN_GANADOR_;
        $this->acumularSetsGanados();
        if($this->setGanadosLocal >= $setsParaGanar) {
            $this->ganador = self::_LOCAL_;
            $this->perdedor = self::_VISITANTE_;
            $this->nombreGanador = $this->nombreJugadorLocal;
            $this->nombrePerdedor = $this->nombreJugadorVisitante;
        }

        if($this->setGanadosVisitante >= $setsParaGanar) {
            $this->ganador = self::_VISITANTE_;
            $this->perdedor = self::_LOCAL_;
            $this->nombreGanador = $this->nombreJugadorVisitante;
            $this->nombrePerdedor = $this->nombreJugadorLocal;
        }
    }

    private function acumularSetsGanados()
    {
        foreach ($this->sets as $set) {
            $ganador = $set->getGanadorSet();
            if($ganador == Set::_LOCAL_) {
                $this->setGanadosLocal++;
            }
            if($ganador == Set::_VISITANTE_) {
                $this->setGanadosVisitante++;
            }
        }
    }

    function getNombreGanador()
    {
        $this->getGanador();
        return $this->nombreGanador;
    }

    function getNombrePerdedor()
    {
        $this->getGanador();
        return $this->nombrePerdedor;
    }


}