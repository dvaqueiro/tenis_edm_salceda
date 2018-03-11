<?php

namespace Application\CourtBooking;

use Carbon\Carbon;
use Domain\Model\ReservaRespository;
use Domain\Model\Reservas;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class HorasLibresReservaCommandHandler
{
    private $horasLibresRepository;

    function __construct(ReservaRespository $horasLibresRepository)
    {
        $this->horasLibresRepository = $horasLibresRepository;
    }

    public function handle(HorasLibresReservaCommand $command)
    {
        $horasLibres = [];
        $detalleReservas = [];
        $pista = $command->getPista();
        $fecha = Carbon::createFromFormat('d-m-Y', $command->getFecha());

        if ($fecha->isWeekend() || $fecha->isFriday()) {
            $arrayReservas = $this->horasLibresRepository->findByPistaYFecha($pista, $fecha);

            if ($fecha->isFriday()) {
                $horasDisponibles = [
                    6 => '20:00 a 22:00',
                ];
            } else {
                $horasDisponibles = [
                    1 => '10:00 a 12:00',
                    2 => '12:00 a 14:00',
                    3 => '14:00 a 16:00',
                    4 => '16:00 a 18:00',
                    5 => '18:00 a 20:00',
                    6 => '20:00 a 22:00',
                ];
            }

            $reservas = new Reservas($horasDisponibles);
            $reservas->setReservas($arrayReservas);
            $horasLibres = $reservas->getHorasLibres();
            $detalleReservas = $reservas->getDetalleReservas();
        }

        return [
            'pista' => $pista,
            'fecha' => $fecha,
            'horas_libres' => $horasLibres,
            'reservas'     => $detalleReservas,
        ];
    }
}