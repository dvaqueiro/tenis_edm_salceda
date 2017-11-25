<?php

use Application\JugadoresPorLigaCommand;
use Application\JugadoresPorLigaHandler;
use Domain\Model\ArrayDivisionFactory;
use Domain\Model\ArrayJugadorFactory;
use Domain\Model\ArrayLigaFactory;
use Infrastructure\Persistence\DbalDivisionRepository;
use Infrastructure\Persistence\DbalJugadorRepository;
use Infrastructure\Persistence\DbalLigaRepository;
use League\Tactician\CommandBus;
use League\Tactician\Doctrine\DBAL\TransactionMiddleware;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\Locator\InMemoryLocator;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;
use League\Tactician\Plugins\LockingMiddleware;
use Silex\Application;
use Silex\Provider\AssetServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\TwigServiceProvider;

$app = new Application();

/**
 * ServicePrividers
 */
$app->register(new ServiceControllerServiceProvider());
$app->register(new AssetServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new HttpFragmentServiceProvider());
$app->register(new DoctrineServiceProvider(), array(
    'db.options' => array (
        'driver'    => 'pdo_mysql',
        'host'      => '127.0.0.1',
        'dbname'    => 'u298739358_edms',
        'user'      => 'root',
        'password'  => 'root',
        'charset'   => 'utf8mb4',
    )
));
$app['twig'] = $app->extend('twig', function ($twig, $app) {
    return $twig;
});

/**
 * Factories
 */
$app['liga_factory'] = $app->factory(function ($app) {
    return new ArrayLigaFactory();
});

$app['division_factory'] = $app->factory(function ($app) {
    return new ArrayDivisionFactory();
});

$app['jugador_factory'] = $app->factory(function ($app) {
    return new ArrayJugadorFactory();
});

/**
 * CommandHandlers
 */
$app['jugadores_por_liga_handler'] = $app->factory(function ($app) {
    return new JugadoresPorLigaHandler(
        new DbalLigaRepository($app['db'], new ArrayLigaFactory()),
        new DbalDivisionRepository($app['db'], new ArrayDivisionFactory()),
        new DbalJugadorRepository($app['db'], new ArrayJugadorFactory()));
});

/**
 * Command Bus
 */
$app['commandBus'] = function ($app){
    return new CommandBus([
        new LockingMiddleware(),
        new TransactionMiddleware($app['db']),
        new CommandHandlerMiddleware(
            new ClassNameExtractor(),
            new InMemoryLocator([
                JugadoresPorLigaCommand::class => $app['jugadores_por_liga_handler'],
            ]), new HandleInflector()
        )
    ]);
};

return $app;
