<?php

namespace Domain\Model\Resultado;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class ResultadosJugador
{
    private $division;
    private $jugador;
    private $rivales;
    private $rivalesSinJugar;
    private $resultados;


    /**
     *
     * @param Jugador $jugador
     * @param Jugador[] $rivales
     * @param Resultado[] $resultados
     */
    function __construct($division, $jugador, $rivales, $resultados)
    {
        $this->division = $division;
        $this->jugador = $jugador;
        $this->rivales = $rivales;
        $this->rivalesSinJugar = clone $rivales;
        $this->addResultados($resultados);
    }

    /**
     *
     * @param Resultado[] $resultados
     */
    public function addResultados($resultados)
    {
        foreach ($resultados as $resultado) {
            $this->addResultado($resultado);
        }
    }

    public function addResultado(Resultado $resultado)
    {
        $idLocal = $resultado->getIdJugadorLocal();
        $idVisitante = $resultado->getIdJugadorVisitante();
        $contrincanteJugado = ($this->jugador->getId() == $idLocal)?$idVisitante:$idLocal;
        $this->unsetRivalJugado($contrincanteJugado);
        $this->resultados[] = $resultado;
    }

    private function unsetRivalJugado($rivalId)
    {
        foreach ($this->rivalesSinJugar as $key => $rivalSinJugar) {
            if($rivalSinJugar->getId() == $rivalId) {
                unset($this->rivalesSinJugar[$key]);
            }
        }
    }

    function getJugador()
    {
        return $this->jugador;
    }

    function getResultados()
    {
        return $this->resultados;
    }

    function getRivalesSinJugar()
    {
        return $this->rivalesSinJugar;
    }

    function getDivision()
    {
        return $this->division;
    }

    function getRivales()
    {
        return $this->rivales;
    }
}
