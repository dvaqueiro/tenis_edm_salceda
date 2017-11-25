<?php

namespace Domain\Model;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class Liga
{
    private $idLiga;
    private $nombreLiga;
    private $divisiones;

    function __construct($idLiga, $nombreLiga)
    {
        $this->idLiga = $idLiga;
        $this->nombreLiga = $nombreLiga;
        $this->divisiones = [];
    }

    public function addDivision(Division $division)
    {
        $this->divisiones[$division->getIdDivision()] = $division;
    }

    function getIdLiga()
    {
        return $this->idLiga;
    }

    function getNombreLiga()
    {
        return $this->nombreLiga;
    }

    function getDivisiones()
    {
        return $this->divisiones;
    }

}