<?php
namespace Application;

use Domain\Model\ContactForm;
use Swift_Mailer;
use Swift_Message;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class ContactFormCommandHandler
{
    private $config;

    /**
     * @var Swift_Mailer
     */
    private $mailer;

    function __construct(Swift_Mailer $mailer, $config)
    {
        $this->mailer = $mailer;
        $this->config = $config;
    }

    public function handle(ContactFormCommand $command)
    {
        $contactForm = $command->getContactForm();

        $message = new Swift_Message('Correo de contacto desde la web de la liga edmsalceda');
        $message->setFrom(array($contactForm->getEmail()))
                ->setTo($this->config['to.manager'])
                ->setTo($this->config['to.admin'])
                ->setBody($this->generateMessageContent($contactForm), 'text/html');

        $ok = $this->mailer->send($message);

        if($ok) {
            $out = "Tu mensaje ha sido enviado correctamente a los organizadores.";
        }
    }

    private function generateMessageContent(ContactForm $contactForm)
    {
        $normalizeComment = nl2br($contactForm->getComment());
        return "<table width='500' border='0' cellspacing='5' cellpadding='5'>
            <tr>
                <td width='98' bgcolor='#CDFDC6'><strong>Nombre</strong></td>
                <td width='387' bgcolor='#EEFFDF'>{$contactForm->getName()}</td>
            </tr>
            <tr>
                <td bgcolor='#CDFDC6'><strong>E-mail</strong></td>
                <td bgcolor='#EEFFDF'>{$contactForm->getEmail()}</td>
            </tr>
            <tr>
                <td valign='top' bgcolor='#CDFDC6'><strong>Comentario</strong></td>
                <td bgcolor='#EEFFDF'>'.nl2br({$normalizeComment}).'</td>
            </tr>
          </table>";
    }
}