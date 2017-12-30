<?php

namespace Domain\Model;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class Jugador
{
    private $foto;
    private $fotoFile;
    private $password;
    private $email;
    private $telefono;
    private $nombre;
    private $dni;
    private $id;
    private $roles;

    function __construct($id, $dni, $nombre, $telefono, $email, $password, $foto, $roles)
    {

        $this->id = $id;
        $this->dni = $dni;
        $this->nombre = mb_convert_case(mb_strtolower($nombre), MB_CASE_TITLE, "UTF-8");
        $this->telefono = $telefono;
        $this->email = $email;
        $this->password = $password;
        $this->foto = $foto;
        $this->roles = $roles;
    }

    function getFotoFile()
    {
        return $this->fotoFile;
    }

    function setFotoFile($fotoFile)
    {
        $this->fotoFile = $fotoFile;
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
        if($password != null) {
            $this->password = password_hash(trim($password), PASSWORD_BCRYPT, ['cost' => 13]);
        }
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
        $this->nombre = mb_convert_case(mb_strtolower($nombre), MB_CASE_TITLE, "UTF-8");
    }

    function setDni($dni)
    {
        $this->dni = $dni;
    }
    
    function setId($id)
    {
        $this->id = $id;
    }

    function getRoles()
    {
        return $this->roles;
    }

    function setRoles($roles)
    {
        $this->roles = $roles;
    }

}