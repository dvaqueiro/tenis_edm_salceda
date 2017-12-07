<?php

namespace Application\CourtBooking;

use Ddd\Domain\DomainEventPublisher;
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

        if(!$reserva->checkToken($token)) {
            $out['ok'] = false;
            $out['message'] = "Invalid token";
        }

        $reserva->setAprobado(true);

        $ok = $this->reservaRepository->update($reserva);

        if($ok) {
            $out['ok'] = true;
            $out['message'] = "Reserva aprobada correctamente";

            DomainEventPublisher::instance()->publish(
                new CourtBookingWasConfirmedEvent($reserva->getId())
            );
        }

        return $out;
    }
}