<?php

namespace Application\CourtBooking;

use Ddd\Domain\DomainEventSubscriber;
use Domain\Model\Jugador;
use Domain\Model\JugadorRepository;
use Domain\Model\Reserva;
use Domain\Model\ReservaRespository;
use Swift_Mailer;
use Swift_Message;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class SendMailToBookingConfirmationSuscriber implements DomainEventSubscriber
{
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
        Swift_Mailer $mailer, $config)
    {
        $this->reservaRepository = $reservaRepository;
        $this->mailer = $mailer;
        $this->jugadorRepository = $jugadorRepository;
        $this->config = $config;
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

        $message = new Swift_Message();
        $message->setSubject($this->config['subject']);
        $message->setFrom($this->config['from']);
        $message->setTo($this->config['to']);
        $message->setBody($this->body);

        $ok = $this->mailer->send($message);
        \Symfony\Component\VarDumper\VarDumper::dump($ok);
        \Symfony\Component\VarDumper\VarDumper::dump($message);
    }

    public function isSubscribedTo($aDomainEvent): bool
    {
        return $aDomainEvent instanceof CourtBookingWasCreatedEvent;
    }

    public function buildEmailBody(Reserva $reserva, Jugador $jugador)
    {
        $this->body = ($reserva->getPista() == 1)? "Reserva Pabellón de Parderrubias":"Reserva pista exterior de Parderrubias";

        $this->body .= "<table width='500' border='0' cellspacing='5' cellpadding='5'>
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
                <td bgcolor='#EEFFDF'>{$reserva->getFecha()} de {$reserva->getHoraTexto()}</td>
            </tr>
        </table>";
    }
}