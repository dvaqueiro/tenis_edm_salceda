<?php

namespace Infrastructure\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Domain\Model\Jugador;
use Domain\Model\JugadorFactory;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class ArrayJugadorFactory implements JugadorFactory
{
    public function makeAll($data)
    {
        $jugadores = new ArrayCollection();
        foreach ($data as $objData) {
            $jugadores->set($objData['id'], new Jugador($objData['id'], $objData['dni'], $objData['nombre'], $objData['telefono'],
                $objData['email'], $objData['password'], $objData['foto'], $objData['roles']));
        }

        return $jugadores;
    }

    public function make($data)
    {
        return new Jugador($data['id'], $data['dni'], $data['nombre'], $data['telefono'],
                $data['email'], $data['password'], $data['foto'], $data['roles']);
    }
}