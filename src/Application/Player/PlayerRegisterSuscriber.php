<?php

namespace Application\Player;

use Ddd\Domain\DomainEventSubscriber;
use Domain\Model\Jugador;
use Domain\Model\JugadorRepository;
use Swift_Mailer;
use Swift_Message;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class PlayerRegisterSuscriber implements DomainEventSubscriber
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

    private $body;

    function __construct(JugadorRepository $jugadorRepository, Swift_Mailer $mailer, $config)
    {
        $this->mailer = $mailer;
        $this->jugadorRepository = $jugadorRepository;
        $this->config = $config;
    }

    /**
     *
     * @param PlayerWasRegisterEvent $aDomainEvent
     */
    public function handle($aDomainEvent)
    {
        $jugador = $this->jugadorRepository->findById($aDomainEvent->getJugadorId());
        $this->buildEmailBody($jugador);

        $message = new Swift_Message('Nueva alta de jugador en liga');
        $message->setTo($this->config['to.manager']);
        $message->setBcc($this->config['to.admin']);
        $message->setFrom($this->config['from']);
        $message->setBody($this->body, 'text/html');

        $ok = $this->mailer->send($message);
    }

    public function isSubscribedTo($aDomainEvent): bool
    {
        return $aDomainEvent instanceof PlayerWasRegisterEvent;
    }

    public function buildEmailBody(Jugador $jugador)
    {
        $this->body = "<h2>Un nuevo jugador ha solicitado inscribirse a la liga con los siguientes datos...</h2>";
        $this->body .= "<table width='500' border='0' cellspacing='5' cellpadding='5'>
            <tr>
                <td width='98' bgcolor='#CDFDC6'><strong>DNI</strong></td>
                <td width='387' bgcolor='#EEFFDF'>{$jugador->getDni()}</td>
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
        </table>";
    }
}