<?php

namespace Domain\Model;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class Jugador
{
    private $foto;
    private $password;
    private $email;
    private $telefono;
    private $nombre;
    private $dni;
    private $id;

    function __construct($id, $dni, $nombre, $telefono, $email, $password, $foto)
    {

        $this->id = $id;
        $this->dni = $dni;
        $this->nombre = $nombre;
        $this->telefono = $telefono;
        $this->email = $email;
        $this->password = $password;
        $this->foto = $foto;
    }

    function getFoto()
    {
        return $this->foto;
    }

    function getPassword()
    {
        return $this->password;
    }

    function getEmail()
    {
        return $this->email;
    }

    function getTelefono()
    {
        return $this->telefono;
    }

    function getNombre()
    {
        return $this->nombre;
    }

    function getDni()
    {
        return $this->dni;
    }

    function getId()
    {
        return $this->id;
    }

}