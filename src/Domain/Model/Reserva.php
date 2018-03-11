<?php

namespace Domain\Model;

use DateTime;
use DateTimeInterface;
use function random_bytes;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class Reserva
{
    private $nombreJugador;

    const _PABELLON_ = 1;
    const _EXTERIOR_ = 2;

    private $id;
    private $idJugador;
    private $pista;
    private $fecha;
    private $hora;
    private $aprobado;
    private $horas = [
        1 => '10:00 a 12:00',
        2 => '12:00 a 14:00',
        3 => '14:00 a 16:00',
        4 => '16:00 a 18:00',
        5 => '18:00 a 20:00',
        6 => '20:00 a 22:00',
    ];
    private $token;

    function __construct($id, $idJugador, $nombreJugador, $pista, $fecha, $hora, $aprobado, $token = null)
    {
        $this->hora = $hora;
        $this->fecha = $fecha;
        $this->pista = $pista;
        $this->id = $id;
        $this->idJugador = $idJugador;
        $this->aprobado = (null == $aprobado)? 0 : $aprobado;
        $this->token = ($token)?$token:bin2hex(random_bytes(10));
        $this->nombreJugador = $nombreJugador;
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

    /**
     *
     * @return DateTimeInterface
     */
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

    function getNombreJugador()
    {
        return $this->nombreJugador;
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

    public function getHoraTexto()
    {
        return $this->horas[$this->hora];
    }

    public function checkToken($token)
    {
        return $this->token === $token;
    }

    function getToken()
    {
        return $this->token;
    }
}