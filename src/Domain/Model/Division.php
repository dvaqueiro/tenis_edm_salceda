<?php

namespace Domain\Model;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class Division
{
    private $idDivision;
    private $idLiga;
    private $nombre;
    private $categoria;
    private $participantes;

    function __construct($idDivision, $idLiga, $nombre, $categoria)
    {
        $this->idDivision = $idDivision;
        $this->idLiga = $idLiga;
        $this->nombre = $nombre;
        $this->categoria = $categoria;
    }

    function getIdDivision()
    {
        return $this->idDivision;
    }

    function getIdLiga()
    {
        return $this->idLiga;
    }

    function getNombre()
    {
        return $this->nombre;
    }

    function getCategoria()
    {
        return $this->categoria;
    }

    public function setParticipantes($jugadores)
    {
        $this->participantes = $jugadores;
    }

    function getParticipantes()
    {
        return $this->participantes;
    }

}