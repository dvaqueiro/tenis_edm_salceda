<?php

namespace Application\CourtBooking;

use Ddd\Domain\DomainEventPublisher;
use Domain\Model\ReservaRespository;
use Swift_Mailer;

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
        $newReserva = $command->getReserva();
        $newReserva = $this->reservaRepository->add($newReserva);
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