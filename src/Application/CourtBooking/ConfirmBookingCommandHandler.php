<?php

namespace Application\CourtBooking;

use Ddd\Domain\DomainEventPublisher;
use Domain\Model\Reserva;
use Domain\Model\ReservaRespository;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class ConfirmBookingCommandHandler
{
    /**
     * @var ReservaRespository
     */
    private $reservaRepository;

    function __construct(ReservaRespository $reservaRepository)
    {
        $this->reservaRepository = $reservaRepository;
    }

    public function handle(ConfirmBookingCommand $command)
    {
        $out['ok'] = false;
        $out['message'] = "Se ha producido un error al intentar confirmar la reserva";

        $reservaId = $command->getIdReserva();
        $reserva = $this->reservaRepository->findById($reservaId);
        $token = $command->getToken();
        $confirmar = $command->getConfirmado();

        if(!$reserva->checkToken($token)) {
            $out['ok'] = false;
            $out['message'] = "Invalid token";
        }

        $reserva->setAprobado($confirmar);

        $ok = $this->reservaRepository->update($reserva);

        if($ok && $confirmar == Reserva::_APROBADO_) {
            $out['ok'] = true;
            $out['message'] = "Reserva aprobada correctamente";

            DomainEventPublisher::instance()->publish(
                new CourtBookingWasConfirmedEvent($reserva->getId())
            );
        }

        if($ok && $confirmar == Reserva::_RECHAZADO_) {
            $out['ok'] = true;
            $out['message'] = "Reserva rechazada correctamente";

            DomainEventPublisher::instance()->publish(
                new CourtBookingWasRejectEvent($reserva->getId())
            );
        }

        return $out;
    }
}