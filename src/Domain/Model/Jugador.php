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
        $this->nombre = mb_convert_case(mb_strtolower($nombre), MB_CASE_TITLE, "UTF-8");
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

    function setFoto($foto)
    {
        $this->foto = $foto;
    }

    function setPassword($password)
    {
        $this->password = $password;
    }

    function setEmail($email)
    {
        $this->email = $email;
    }

    function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }

    function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    function setDni($dni)
    {
        $this->dni = $dni;
    }
    
    function setId($id)
    {
        $this->id = $id;
    }

}