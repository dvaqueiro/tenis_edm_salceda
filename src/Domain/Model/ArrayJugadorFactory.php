<?php
namespace Domain\Model;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class ArrayJugadorFactory implements JugadorFactory
{
    public function makeAll($data)
    {
        foreach ($data as $objData) {
            $out[] = new Jugador($objData['id'], $objData['dni'], $objData['nombre'], $objData['telefono'], 
                $objData['email'], $objData['contrasena'], $objData['foto']);
        }

        return $out;
    }
}