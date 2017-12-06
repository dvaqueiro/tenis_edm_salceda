<?php

namespace Domain\Model;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
interface ReservaRespository
{

    /**
     *
     * @param \Domain\Model\Reserva $newReserva
     * @return Reserva
     */
    public function add(Reserva $newReserva);

    public function findByPistaYFecha($pista, \DateTime $fecha);
}