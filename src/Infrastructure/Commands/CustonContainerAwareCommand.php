<?php

namespace Infrastructure\Commands;

use Silex\Application;
use Symfony\Component\Console\Command\Command;

/**
 * @author <dvaqueiro at boardfy dot com>
 */
abstract class CustonContainerAwareCommand extends Command
{
    /**
     *
     * @var Application
     */
    protected $app;

    function __construct(Application $app, $name = null)
    {
        parent::__construct($name);
        $this->app = $app;
    }
}