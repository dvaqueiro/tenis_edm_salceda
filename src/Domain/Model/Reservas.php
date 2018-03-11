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

    public function __construct($horasLibres)
    {
        $this->horasLibres = $horasLibres;
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

    function getDetalleReservas()
    {
        $out = [];

        foreach ($this->reservas as $reserva) {
            /* @var $reserva Reserva */
            $estado = ($reserva->getAprobado()) ? '(Aprobada)' : '(En curso)';
            $out[] = [
                'hora'    => $reserva->getHoraTexto(),
                'jugador' => $reserva->getNombreJugador(),
                'estado'  => $estado
            ];
        }

        return $out;
    }
}