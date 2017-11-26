<?php

namespace Application;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class AddComentarioCommand
{
    private $contenido;
    private $usuario;

    function __construct($usuario, $contenido)
    {
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

}