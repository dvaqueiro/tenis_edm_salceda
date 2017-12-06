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
            1 => '10 a 12',
            2 => '12 a 14',
            3 => '14 a 16',
            4 => '16 a 18',
            5 => '18 a 20',
            6 => '20 a 22',
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