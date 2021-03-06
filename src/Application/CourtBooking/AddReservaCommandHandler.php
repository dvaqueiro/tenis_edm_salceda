<?php

namespace Application\CourtBooking;

use Carbon\Carbon;
use Ddd\Domain\DomainEventPublisher;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Domain\Model\Reserva;
use Domain\Model\ReservaException;
use Domain\Model\ReservaRespository;
use Swift_Mailer;
use Symfony\Component\VarDumper\VarDumper;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class AddReservaCommandHandler
{
    private $reservaRepository;

    function __construct(ReservaRespository $reservaRepository)
    {
        $this->reservaRepository = $reservaRepository;
    }

    public function handle(AddReservaCommand $command)
    {
        /* @var $newReserva Reserva */
        $newReserva = $command->getReserva();

        $datetime = $newReserva->getFecha();
        $carbonReserva = Carbon::instance($datetime);

        $esViernesALasOcho = ($carbonReserva->isFriday() && $carbonReserva->hour = 20);
        if ($carbonReserva->isWeekday() && ! $esViernesALasOcho) {
            throw new ReservaException("La reserva del pabellón solo está disponible para los fines de semana y los viernes a las 20:00");
        }

        $carbonHoy = Carbon::now();
        $esViernesDespuesDeLasDoce = $carbonHoy->isWeekend() || ($carbonHoy->isFriday() && $carbonHoy->hour < 12);

        if ($carbonHoy->weekOfYear == $carbonReserva->weekOfYear && $esViernesDespuesDeLasDoce) {
            throw new ReservaException("La reserva del pabellón tendrá que realizarse antes del viernes a las 12:00 de cada semana");
        }

        try{
            $newReserva = $this->reservaRepository->add($newReserva);
        } catch (UniqueConstraintViolationException $ex) {
            throw new ReservaException("Ya existe una reserva para la misma fecha y hora");
        }
        
        $out = "Tu reserva ha sido registrada en el sistema, recibirás un email cuando sea confirmada por los organizadores.";
        if(!$newReserva->getId()) {
            $out = "Se ha producido un error al registrar tu reserva, vuelve a intentarlo o ponte en contacto con los organizadores.";
        }

        DomainEventPublisher::instance()->publish(
            new CourtBookingWasCreatedEvent($newReserva->getId())
        );

        return $out;
    }
}