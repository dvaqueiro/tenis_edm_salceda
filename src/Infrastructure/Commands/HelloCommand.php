<?php

namespace Infrastructure\Commands;

use Symfony\Bridge\Monolog\Handler\ConsoleHandler;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author <dvaqueiro at boardfy dot com>
 */
class HelloCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('hello')
            ->setDescription('Muestra un hola mundo en pantalla');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $monolog = new Logger('test logger');
        $monolog->pushHandler(new ConsoleHandler($output, true,
            array(OutputInterface::VERBOSITY_NORMAL => Logger::DEBUG)));
        $monolog->info('hello world');
    }
}