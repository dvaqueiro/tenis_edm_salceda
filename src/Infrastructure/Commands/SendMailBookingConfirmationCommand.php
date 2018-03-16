<?php

namespace Infrastructure\Commands;

use Application\CourtBooking\SendMailToBookingConfirmationCommand;
use Infrastructure\Commands\CustonContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author <dvaqueiro at boardfy dot com>
 */
class SendMailBookingConfirmationCommand extends CustonContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('booking:send')
            ->addArgument('reserva_id', InputArgument::REQUIRED, 'Identificador de la reserva')
            ->setDescription('Envía el email de petición para una determinada reserva');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app['swiftmailer.use_spool'] = false;
        $reservaId = $input->getArgument('reserva_id');
        $commandHandler = $this->app['send_mail_to_booking_confirmation_command_handler'];
        $ok = $commandHandler->handle(new SendMailToBookingConfirmationCommand($reservaId));

        if($ok) {
            $text = "Mensaje para la reserva {$reservaId} enviado correctamente.";
        } else {
            $text = "Se ha producido un error al enviar el mensaje para la reserva {$reservaId}";
        }

        $output->writeln($text);
    }

}