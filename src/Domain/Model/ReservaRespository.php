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
     * @param Reserva $reserva
     */
    public function update(Reserva $reserva);

    /**
     *
     * @param int $reservaId
     * @return Reserva
     */
    public function findById($reservaId);

    /**
     *
     * @param \Domain\Model\Reserva $newReserva
     * @return Reserva
     */
    public function add(Reserva $newReserva);

    /**
     *
     * @param int $pista
     * @param \DateTime $fecha
     * @return Reserva[]
     */
    public function findByPistaYFecha($pista, \DateTime $fecha);
}