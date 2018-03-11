<?php

namespace Infrastructure\Model;

use DateTime;
use Domain\Model\Reserva;
use Domain\Model\ReservaFactory;

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
            $reservas[] = new Reserva($row['id'], $row['idusuario'], $row['nombre'], $row['pista'],
                DateTime::createFromFormat('Y-m-d', $row['fecha']), $row['hora'], $row['aprobado'], $row['token']);
        }

        return $reservas;
    }

    public function make($data)
    {
        return new Reserva($data['id'], $data['idusuario'], $data['nombre'], $data['pista'],
            DateTime::createFromFormat('Y-m-d', $data['fecha']), $data['hora'], $data['aprobado'], $data['token']);
    }
}