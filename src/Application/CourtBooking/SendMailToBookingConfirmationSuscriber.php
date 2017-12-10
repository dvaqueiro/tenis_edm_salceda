<?php

namespace Application\CourtBooking;

use Ddd\Domain\DomainEventSubscriber;
use Domain\Model\Jugador;
use Domain\Model\JugadorRepository;
use Domain\Model\Reserva;
use Domain\Model\ReservaRespository;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class SendMailToBookingConfirmationSuscriber implements DomainEventSubscriber
{
    /**
     * @var UrlGenerator
     */
    private $urlGenerator;
    private $config;

    /**
     * @var JugadorRepository
     */
    private $jugadorRepository;

    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var ReservaRespository
     */
    private $reservaRepository;
    private $body;

    function __construct(ReservaRespository $reservaRepository, JugadorRepository $jugadorRepository, 
        UrlGenerator $urlGenerator, Swift_Mailer $mailer, $config)
    {
        $this->reservaRepository = $reservaRepository;
        $this->mailer = $mailer;
        $this->jugadorRepository = $jugadorRepository;
        $this->config = $config;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     *
     * @param CourtBookingWasCreatedEvent $aDomainEvent
     */
    public function handle($aDomainEvent)
    {
        $reservaId = $aDomainEvent->getReservaId();
        $reserva = $this->reservaRepository->findById($reservaId);
        $jugador = $this->jugadorRepository->findById($reserva->getIdJugador());
        $this->buildEmailBody($reserva, $jugador);

        $message = new Swift_Message('Confirmar de reserva de pista');
        $message->setTo($this->config['to.booking']);
        $message->setBcc($this->config['to.admin']);
        $message->setFrom($this->config['from']);
        $message->setBody($this->body, 'text/html');

        $ok = $this->mailer->send($message);
    }

    public function isSubscribedTo($aDomainEvent): bool
    {
        return $aDomainEvent instanceof CourtBookingWasCreatedEvent;
    }

    public function buildEmailBody(Reserva $reserva, Jugador $jugador)
    {
        $pista = ($reserva->getPista() == 1)? "Pabellón de Parderrubias":"Pista exterior de Parderrubias";

        $urlConfirmBooking = $this->urlGenerator->generate('booking_confirm', [
            'token' => $reserva->getToken(),
            'idReserva' => $reserva->getId(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $this->body = "<h2>Se ha solicitado una reserva de pista con los siguientes datos...</h2>";
        $this->body .= "<table width='500' border='0' cellspacing='5' cellpadding='5'>
            <tr>
                <td width='98' bgcolor='#CDFDC6'><strong>Pista</strong></td>
                <td width='387' bgcolor='#EEFFDF'>{$pista}</td>
            </tr>
            <tr>
                <td width='98' bgcolor='#CDFDC6'><strong>Nombre</strong></td>
                <td width='387' bgcolor='#EEFFDF'>{$jugador->getNombre()}</td>
            </tr>
            <tr>
                <td width='98' bgcolor='#CDFDC6'><strong>E-Mail</strong></td>
                <td width='387' bgcolor='#EEFFDF'>{$jugador->getEmail()}</td>
            </tr>
            <tr>
                <td width='98' bgcolor='#CDFDC6'><strong>Teléfono</strong></td>
                <td width='387' bgcolor='#EEFFDF'>{$jugador->getTelefono()}</td>
            </tr>
            <tr>
                <td bgcolor='#CDFDC6'><strong>Fecha y hora reservadas</strong></td>
                <td bgcolor='#EEFFDF'>{$reserva->getFecha()->format('d-m-Y')} de {$reserva->getHoraTexto()}</td>
            </tr>
            <tr>
                <td bgcolor='#CDFDC6'><strong>Confirmar Reserva</strong></td>
                <td bgcolor='#EEFFDF'><a href='{$urlConfirmBooking}'>Pulse para confirmar</a></td>
            </tr>
        </table>";
    }
}