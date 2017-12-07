<?php

namespace Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class Reservas
{
    private $horasLibres;
    private $reservas;

    public function __construct()
    {
        $this->horasLibres = [
            1 => '10:00 a 12:00',
            2 => '12:00 a 14:00',
            3 => '14:00 a 16:00',
            4 => '16:00 a 18:00',
            5 => '18:00 a 20:00',
            6 => '20:00 a 22:00',
        ];
        $this->reservas = new ArrayCollection();
    }

    public function addReserva(Reserva $reserva)
    {
        $this->reservas->add($reserva);
    }

    public function setReservas($reservas)
    {
        $this->reservas = new ArrayCollection($reservas);
    }

    function getHorasLibres()
    {
        foreach ($this->reservas as $reserva) {
            /* @var $reserva Reserva */
            unset($this->horasLibres[$reserva->getHora()]);
        }
        return array_flip($this->horasLibres);
    }
}