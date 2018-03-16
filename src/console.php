<?php

use Infrastructure\Commands\FixUsersCommand;
use Infrastructure\Commands\HelloCommand;
use Infrastructure\Commands\SendMailBookingConfirmationCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputOption;


$console = new Application('My Silex Application', getenv('CONSOLE_VERSION'));
$console->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'dev'));
$console->setDispatcher($app['dispatcher']);


$console->addCommands(array(
    new HelloCommand(),
    new FixUsersCommand($app),
    new SendMailBookingConfirmationCommand($app),
));

return $console;
