<?php

namespace Application;

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
        $pista = $command->getPista();
        $fecha = \Carbon\Carbon::createFromFormat('d-m-Y', $command->getFecha());

        $arrayReservas = $this->horasLibresRepository->findByPistaYFecha($pista, $fecha);

        $reservas = new Reservas();
        $reservas->setReservas($arrayReservas);

        $horasLibres = $reservas->getHorasLibres();
        
        return [
            'pista' => $pista,
            'fecha' => $fecha,
            'horas_libres' => $horasLibres,
        ];
    }
}