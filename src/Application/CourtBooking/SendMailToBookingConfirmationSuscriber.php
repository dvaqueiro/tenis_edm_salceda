<?php

namespace Application\CourtBooking;

use Ddd\Domain\DomainEventSubscriber;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class SendMailToBookingConfirmationSuscriber implements DomainEventSubscriber
{
    /**
     * @var SendMailToBookingConfirmationCommandHandler
     */
    private $commandHandler;

    function __construct(SendMailToBookingConfirmationCommandHandler $commandHandler)
    {
        $this->commandHandler = $commandHandler;
    }

    /**
     *
     * @param CourtBookingWasCreatedEvent $aDomainEvent
     */
    public function handle($aDomainEvent)
    {
        $reservaId = $aDomainEvent->getReservaId();
        $this->commandHandler->handle(new SendMailToBookingConfirmationCommand($reservaId));
    }

    public function isSubscribedTo($aDomainEvent): bool
    {
        return $aDomainEvent instanceof CourtBookingWasCreatedEvent;
    }

}