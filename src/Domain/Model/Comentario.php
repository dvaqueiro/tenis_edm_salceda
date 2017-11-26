<?php

namespace Domain\Model;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class Comentario
{
    private $contenido;
    private $usuario;
    private $fecha;
    private $id;

    function __construct($id, $fecha, $usuario, $contenido)
    {
        $this->id = $id;
        $this->fecha = new \DateTime($fecha);
        $this->usuario = $usuario;
        $this->contenido = $contenido;
    }

    function getContenido()
    {
        return $this->contenido;
    }

    function getUsuario()
    {
        return $this->usuario;
    }

    function getFecha()
    {
        return $this->fecha;
    }

    function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
}