<?php

use Application\AddComentarioCommand;
use Application\AddComentarioCommandHandler;
use Application\AddResultadoCommad;
use Application\AddResultadoCommandHandler;
use Application\AllAboutDivisionCommand;
use Application\AllAboutDivisionCommandHandler;
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
use Application\Leagues\AllLigasCommand;
use Application\Leagues\AllLigasCommandHandler;
use Application\Player\AddJugadorCommand;
use Application\Player\AddJugadorCommandHandler;
use Application\Player\AllPlayersCommand;
use Application\Player\AllPlayersCommandHandler;
use Application\Player\DeleteJugadorCommand;
use Application\Player\DeleteJugadorCommandHandler;
use Application\Player\PlayerRegisterSuscriber;
use Application\Player\RegisterJugadorCommand;
use Application\Player\RegisterJugadorCommandHandler;
use Application\Player\UpdateJugadorCommand;
use Application\Player\UpdateJugadorCommandHandler;
use Application\RankingCommand;
use Application\RankingCommandHandler;
use Application\ResultadosPorLigaCommand;
use Application\ResultadosPorLigaCommandHandler;
use Ddd\Domain\DomainEventPublisher;
use Domain\Model\BookingChecker;
use Infrastructure\Events\DbalEventRepository;
use Infrastructure\Events\DomainEventsMiddelware;
use Infrastructure\Model\ArrayComentarioFactory;
use Infrastructure\Model\ArrayDivisionFactory;
use Infrastructure\Model\ArrayJugadorFactory;
use Infrastructure\Model\ArrayLigaFactory;
use Infrastructure\Model\ArrayReservaFactory;
use Infrastructure\Model\Resultado\ArrayResultadoFactory;
use Infrastructure\Model\Resultado\DbalResultadoRepository;
use Infrastructure\Persistence\DbalComentarioRepository;
use Infrastructure\Persistence\DbalDivisionRepository;
use Infrastructure\Persistence\DbalJugadorRepository;
use Infrastructure\Persistence\DbalLigaRepository;
use Infrastructure\Persistence\DbalReservaRespository;
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
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../config/.env');

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
        'driver'    => getenv('DB_DRIVER'),
        'host'      => getenv('DB_HOST'),
        'dbname'    => getenv('DB_DBNAME'),
        'user'      => getenv('DB_USER'),
        'password'  => getenv('DB_PASSWORD'),
        'charset'   => getenv('DB_CHARSET'),
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
        'standings' => array('pattern' => '^/standings$'),
        'ranking' => array('pattern' => '^/ranking$'),
        'contact' => array('pattern' => '^/contact$'),
        'facebook' => array('pattern' => '^/facebook$'),
        'booking_confirm' => array('pattern' => '^/courts/confirm/.*$'),
        'login' => array('pattern' => '^/login$'),
        'default' => array(
            'pattern' => '^.*$',
            'form' => array('login_path' => '/login', 'check_path' => '/login_check'),
            'logout' => array('logout_path' => '/logout', 'invalidate_session' => true),
            'users' => $app['user_provider'],
        ),
    ),
    'security.access_rules' => array(
        array('^/admin$', 'ROLE_ADMIN'),
        array('^/.+$', ['ROLE_USER','ROLE_ADMIN']),
    )
));

/**
 * Services
 */
$app['photo_uploader_service'] = $app->factory(function ($app) {
    return new FileUploader($app['photos_directory'], $app['photos_public_directory']);
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
        $app['resultado_repository'],
        $app['jugador_repository']
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
    return new ContactFormCommandHandler($app['mailer'], $app['mail.config']);
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

$app['update_jugador_command_handler'] = $app->factory(function ($app) {
    return new UpdateJugadorCommandHandler(
        $app['jugador_repository'], $app['photo_uploader_service']
    );
});

$app['add_resultado_command_handler'] = $app->factory(function ($app) {
    return new AddResultadoCommandHandler($app['resultado_repository']);
});

$app['all_players_command_handler'] = $app->factory(function ($app) {
    return new AllPlayersCommandHandler($app['jugador_repository']);
});

$app['add_jugador_command_handler'] = $app->factory(function ($app) {
    return new AddJugadorCommandHandler(
        $app['jugador_repository'], $app['photo_uploader_service']
    );
});

$app['delete_jugador_command_handler'] = $app->factory(function ($app) {
    return new DeleteJugadorCommandHandler(
        $app['jugador_repository'], $app['photo_uploader_service']
    );
});

$app['all_leagues_command_handler'] = $app->factory(function ($app) {
    return new AllLigasCommandHandler($app['liga_repository'],$app['division_repository']);
});

$app['all_about_division_command_handler'] = $app->factory(function ($app) {
    return new AllAboutDivisionCommandHandler(
        $app['liga_repository'],
        $app['division_repository'],
        $app['resultado_repository'],
        $app['jugador_repository']
    );
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
                UpdateJugadorCommand::class => $app['update_jugador_command_handler'],
                AddResultadoCommad::class => $app['add_resultado_command_handler'],
                AllPlayersCommand::class => $app['all_players_command_handler'],
                AddJugadorCommand::class => $app['add_jugador_command_handler'],
                DeleteJugadorCommand::class => $app['delete_jugador_command_handler'],
                AllLigasCommand::class => $app['all_leagues_command_handler'],
                AllAboutDivisionCommand::class => $app['all_about_division_command_handler'],
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

    DomainEventPublisher::instance()->subscribe(
       new PlayerRegisterSuscriber(
        $app['jugador_repository'],
        $app['mailer'],
        $app['mail.config']
    ));
});

return $app;
