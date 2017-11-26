<?php

use Application\ClasificacionPorLigaCommand;
use Application\ClasificacionPorLigaHandler;
use Application\JugadoresPorLigaCommand;
use Application\JugadoresPorLigaHandler;
use Application\RankingCommand;
use Application\RankingCommandHandler;
use Application\ResultadosPorLigaCommand;
use Application\ResultadosPorLigaHandler;
use Domain\Model\ArrayDivisionFactory;
use Domain\Model\ArrayJugadorFactory;
use Domain\Model\ArrayLigaFactory;
use Domain\Model\ArrayResultadoFactory;
use Infrastructure\Persistence\DbalDivisionRepository;
use Infrastructure\Persistence\DbalJugadorRepository;
use Infrastructure\Persistence\DbalLigaRepository;
use Infrastructure\Persistence\DbalResultadoRepository;
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

$app['resultado_factory'] = $app->factory(function ($app) {
    return new ArrayResultadoFactory();
});

/**
 * Repositories
 */
$app['liga_repository'] = $app->factory(function ($app) {
    return new DbalLigaRepository($app['db'], $app['liga_factory']);
});

$app['division_repository'] = $app->factory(function ($app) {
    return new DbalDivisionRepository($app['db'], $app['division_factory']);
});

$app['jugador_repository'] = $app->factory(function ($app) {
    return new DbalJugadorRepository($app['db'], $app['jugador_factory']);
});

$app['resultado_repository'] = $app->factory(function ($app) {
    return new DbalResultadoRepository($app['db'], $app['resultado_factory']);
});


/**
 * CommandHandlers
 */
$app['jugadores_por_liga_handler'] = $app->factory(function ($app) {
    return new JugadoresPorLigaHandler(
        $app['liga_repository'],
        $app['division_repository'],
        $app['jugador_repository']
    );
});

$app['resultados_por_liga_handler'] = $app->factory(function ($app) {
    return new ResultadosPorLigaHandler(
        $app['liga_repository'],
        $app['division_repository'],
        $app['resultado_repository']
    );
});

$app['clasificacion_por_liga_handler'] = $app->factory(function ($app) {
    return new ClasificacionPorLigaHandler(
        $app['liga_repository'],
        $app['division_repository'],
        $app['resultado_repository']
    );
});

$app['ranking_command_handler'] = $app->factory(function ($app) {
    return new RankingCommandHandler(
        $app['liga_repository'],
        $app['division_repository'],
        $app['resultado_repository']
    );
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
                ResultadosPorLigaCommand::class => $app['resultados_por_liga_handler'],
                ClasificacionPorLigaCommand::class => $app['clasificacion_por_liga_handler'],
                RankingCommand::class => $app['ranking_command_handler'],
            ]), new HandleInflector()
        )
    ]);
};

return $app;
