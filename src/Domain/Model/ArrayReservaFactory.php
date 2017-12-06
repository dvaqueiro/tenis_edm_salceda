<?php

namespace Domain\Model;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class ArrayReservaFactory implements ReservaFactory
{

    public function makeAll($data)
    {
        $reservas = [];
        foreach ($data as $row) {
            $reservas[] = new Reserva($row['id'], $row['idusuario'], $row['pista'], $row['fecha'], $row['hora']);
        }

        return $reservas;
    }
}