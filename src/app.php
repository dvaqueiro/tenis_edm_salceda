<?php

use Application\AddComentarioCommand;
use Application\AddComentarioCommandHandler;
use Application\ClasificacionPorLigaCommand;
use Application\ClasificacionPorLigaHandler;
use Application\ComentariosCommand;
use Application\ComentariosCommandHandler;
use Application\ContactFormCommand;
use Application\ContactFormCommandHandler;
use Application\CourtBooking\AddReservaCommand;
use Application\CourtBooking\AddReservaCommandHandler;
use Application\CourtBooking\ConfirmBookingCommand;
use Application\CourtBooking\ConfirmBookingCommandHandler;
use Application\CourtBooking\HorasLibresReservaCommand;
use Application\CourtBooking\HorasLibresReservaCommandHandler;
use Application\CourtBooking\SendMailBookingConfirmationSuscriber;
use Application\CourtBooking\SendMailToBookingConfirmationSuscriber;
use Application\JugadoresPorLigaCommand;
use Application\JugadoresPorLigaCommandHandler;
use Application\RankingCommand;
use Application\RankingCommandHandler;
use Application\RegisterJugadorCommand;
use Application\RegisterJugadorCommandHandler;
use Application\ResultadosPorLigaCommand;
use Application\ResultadosPorLigaCommandHandler;
use Ddd\Domain\DomainEventPublisher;
use Domain\Model\ArrayComentarioFactory;
use Domain\Model\ArrayDivisionFactory;
use Domain\Model\ArrayJugadorFactory;
use Domain\Model\ArrayLigaFactory;
use Domain\Model\ArrayReservaFactory;
use Domain\Model\ArrayResultadoFactory;
use Domain\Model\BookingChecker;
use Infrastructure\Events\DbalEventRepository;
use Infrastructure\Events\DomainEventsMiddelware;
use Infrastructure\Persistence\DbalComentarioRepository;
use Infrastructure\Persistence\DbalDivisionRepository;
use Infrastructure\Persistence\DbalJugadorRepository;
use Infrastructure\Persistence\DbalLigaRepository;
use Infrastructure\Persistence\DbalReservaRespository;
use Infrastructure\Persistence\DbalResultadoRepository;
use Infrastructure\Services\FileUploader;
use Infrastructure\UserProvider;
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
use Silex\Provider\FormServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\LocaleServiceProvider;
use Silex\Provider\RoutingServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\SwiftmailerServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Component\HttpFoundation\Request;

$app = new Application();

/**
 * ServiceProviders
 */
$app->register(new ServiceControllerServiceProvider());
$app->register(new AssetServiceProvider());
$app->register(new RoutingServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new HttpFragmentServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new LocaleServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new SessionServiceProvider());
$app->register(new TranslationServiceProvider(), array(
    'translator.domains' => array(),
));
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

$app->register(new SwiftmailerServiceProvider());

$app['twig'] = $app->extend('twig', function ($twig, $app) {
    return $twig;
});

$app['user_provider'] = $app->factory(function() use ($app) {
    return new UserProvider($app['db']);
});

$app->register(new SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'profiler' => array('pattern' => '^/_'), // Example of an url available as anonymous user
        'scores' => array('pattern' => '^/scores$'),
        'facebook' => array('pattern' => '^/facebook$'),
        'booking_confirm' => array('pattern' => '^/courts/confirm/.*$'),
        'login' => array('pattern' => '^/login$'),
        'admin' => array(
            'pattern' => '^/admin',
            'form' => array('login_path' => '/loginadmin', 'check_path' => '/admin/login_check'),
            'logout' => array('logout_path' => '/admin/logout', 'invalidate_session' => true),
            'users' => array(
                // raw password is foo
                'admin' => array('ROLE_ADMIN', '$2y$10$3i9/lVd8UOFIJ6PAMFt8gu3/r5g0qeCJvoSlLCsvMTythye19F77a'),
            ),
        ),
        'default' => array(
            'pattern' => '^.*$',
//            'anonymous' => true, // Needed as the login path is under the secured area
            'form' => array('login_path' => '/login', 'check_path' => '/login_check'),
            'logout' => array('logout_path' => '/logout', 'invalidate_session' => true),
            'users' => $app['user_provider'],
//            'users' => array(
//                // raw password is foo
//                'user' => array('ROLE_USER', '$2y$10$3i9/lVd8UOFIJ6PAMFt8gu3/r5g0qeCJvoSlLCsvMTythye19F77a'),
//            ),
        ),
    ),
    'security.access_rules' => array(
        array('^/admin$', 'ROLE_ADMIN'),
        array('^/.+$', 'ROLE_USER'),
    )
));

/**
 * Services
 */
$app['photo_uploader_service'] = $app->factory(function ($app) {
    return new FileUploader($app['photos_directory']);
});

$app['booking_checker'] = $app->factory(function ($app) {
    return new BookingChecker();
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

$app['comentario_factory'] = $app->factory(function ($app) {
    return new ArrayComentarioFactory();
});

$app['reserva_factory'] = $app->factory(function ($app) {
    return new ArrayReservaFactory();
});

/**
 * Repositories
 */
$app['event_store'] = $app->factory(function ($app) {
    return new DbalEventRepository($app['db']);
});

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

$app['comentario_repository'] = $app->factory(function ($app) {
    return new DbalComentarioRepository($app['db'], $app['comentario_factory']);
});

$app['reserva_repository'] = $app->factory(function ($app) {
    return new DbalReservaRespository($app['db'], $app['reserva_factory']);
});

/**
 * CommandHandlers
 */
$app['jugadores_por_liga_handler'] = $app->factory(function ($app) {
    return new JugadoresPorLigaCommandHandler(
        $app['liga_repository'],
        $app['division_repository'],
        $app['jugador_repository']
    );
});

$app['resultados_por_liga_handler'] = $app->factory(function ($app) {
    return new ResultadosPorLigaCommandHandler(
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

$app['comentarios_command_handler'] = $app->factory(function ($app) {
    return new ComentariosCommandHandler(
        $app['comentario_repository']
    );
});

$app['add_comentario_command_handler'] = $app->factory(function ($app) {
    return new AddComentarioCommandHandler(
        $app['comentario_repository']
    );
});

$app['register_jugador_command_handler'] = $app->factory(function ($app) {
    return new RegisterJugadorCommandHandler(
        $app['jugador_repository'], $app['photo_uploader_service']
    );
});

$app['contactform_command_handler'] = $app->factory(function ($app) {
    return new ContactFormCommandHandler($app['mailer']);
});

$app['horas_libres_reserva_command_handler'] = $app->factory(function ($app) {
    return new HorasLibresReservaCommandHandler($app['reserva_repository']);
});

$app['add_reserva_command_handler'] = $app->factory(function ($app) {
    return new AddReservaCommandHandler($app['reserva_repository']);
});

$app['confirm_booking_command_handler'] = $app->factory(function ($app) {
    return new ConfirmBookingCommandHandler($app['reserva_repository']);
});

/**
 * Command Bus
 */
$app['commandBus'] = function ($app){
    return new CommandBus([
        new LockingMiddleware(),
        new TransactionMiddleware($app['db']),
        new DomainEventsMiddelware($app['event_store']),
        new CommandHandlerMiddleware(
            new ClassNameExtractor(),
            new InMemoryLocator([
                JugadoresPorLigaCommand::class => $app['jugadores_por_liga_handler'],
                ResultadosPorLigaCommand::class => $app['resultados_por_liga_handler'],
                ClasificacionPorLigaCommand::class => $app['clasificacion_por_liga_handler'],
                RankingCommand::class => $app['ranking_command_handler'],
                ComentariosCommand::class => $app['comentarios_command_handler'],
                AddComentarioCommand::class => $app['add_comentario_command_handler'],
                RegisterJugadorCommand::class => $app['register_jugador_command_handler'],
                ContactFormCommand::class => $app['contactform_command_handler'],
                HorasLibresReservaCommand::class => $app['horas_libres_reserva_command_handler'],
                AddReservaCommand::class => $app['add_reserva_command_handler'],
                ConfirmBookingCommand::class => $app['confirm_booking_command_handler'],
            ]), new HandleInflector()
        )
    ]);
};

$app->before(function (Request $request) use ($app) {
    DomainEventPublisher::instance()->subscribe(
       new SendMailToBookingConfirmationSuscriber(
        $app['reserva_repository'],
        $app['jugador_repository'],
        $app['url_generator'],
        $app['mailer'],
        $app['mail.config']
    ));

    DomainEventPublisher::instance()->subscribe(
       new SendMailBookingConfirmationSuscriber(
        $app['reserva_repository'],
        $app['jugador_repository'],
        $app['mailer'],
        $app['mail.config']
    ));
});

return $app;
