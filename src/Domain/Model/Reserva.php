<?php

namespace Domain\Model;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class Reserva
{
    const _PABELLON_ = 1;
    const _EXTERIOR_ = 2;

    private $id;
    private $idJugador;
    private $pista;
    private $fecha;
    private $hora;
    private $aprobado;

    function __construct($id, $idJugador, $pista, $fecha, $hora)
    {
        $this->hora = $hora;
        $this->fecha = $fecha;
        $this->pista = $pista;
        $this->id = $id;
        $this->idJugador = $idJugador;
        $this->aprobado = false;
    }

    function getId()
    {
        return $this->id;
    }

    function getIdJugador()
    {
        return $this->idJugador;
    }

    function getPista()
    {
        return $this->pista;
    }

    function getFecha()
    {
        return $this->fecha;
    }

    function getHora()
    {
        return $this->hora;
    }

    function getAprobado()
    {
        return $this->aprobado;
    }

    function setId($id)
    {
        $this->id = $id;
    }

    function setIdJugador($idJugador)
    {
        $this->idJugador = $idJugador;
    }

    function setPista($pista)
    {
        $this->pista = $pista;
    }

    function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }

    function setHora($hora)
    {
        $this->hora = $hora;
    }

    function setAprobado($aprobado)
    {
        $this->aprobado = $aprobado;
    }
}