<?php

use Application\AddComentarioCommand;
use Application\AddResultadoCommad;
use Application\ClasificacionPorLigaCommand;
use Application\ComentariosCommand;
use Application\ContactFormCommand;
use Application\CourtBooking\AddReservaCommand;
use Application\CourtBooking\ConfirmBookingCommand;
use Application\CourtBooking\HorasLibresReservaCommand;
use Application\JugadoresPorLigaCommand;
use Application\Leagues\AllLigasCommand;
use Application\Player\PlayerResultsCommand;
use Application\Player\PlayerResultsCommandHandler;
use Application\Player\RegisterJugadorCommand;
use Application\Player\UpdateJugadorCommand;
use Application\RankingCommand;
use Application\ResultadosPorLigaCommand;
use Domain\Model\ContactForm;
use Domain\Model\Jugador;
use Domain\Model\PersistenceException;
use Domain\Model\Reserva;
use Domain\Model\ReservaException;
use Domain\Model\Resultado\InvalidResultException;
use Infrastructure\Forms\BookingType;
use Infrastructure\Forms\ContactType;
use Infrastructure\Forms\JugadorType;
use Infrastructure\Forms\JugadorUpdateType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

//Request::setTrustedProxies(array('127.0.0.1'));

$app->mount('/admin', new AdminControllerProvider());

$app->match('/login', function (Request $request) use ($app) {
    $jugador = new Jugador(null, null, null, null, null, null, null, null);
    /* @var $form Form */
    $form = $app['form.factory']->createBuilder(JugadorType::class, $jugador)->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $newJugador = $form->getData();
        $message = $app['commandBus']->handle(new RegisterJugadorCommand($newJugador));
        $app['session']->getFlashBag()->add('mensaje', $message);
        return $app->redirect($request->getUri());
    }

    return $app['twig']->render('login.html.twig', [
        'error'         => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security'),
        'form' => $form->createView(),
    ]);
});

$app->get('/', function (Request $request) use ($app) {
    $token = $app['security.token_storage']->getToken();
    $user = $token->getUser();
    $handler = new PlayerResultsCommandHandler(
        $app['jugador_repository'],
        $app['liga_repository'],
        $app['resultado_repository'],
        $app['division_repository']
    );
    $resultadosJugador = $handler->handle(new PlayerResultsCommand($user->getId()));

    return $app['twig']->render('homepage.html.twig', [
        'resultadosJugador' => $resultadosJugador,
    ]);
})
    ->bind('homepage');

$app->post('/', function (Request $request) use ($app) {
    if ($request->get('form')) {
        try {
            $formData = $request->get('form');
            $app['commandBus']->handle(
                new AddResultadoCommad($formData)
            );
            $app['session']->getFlashBag()->add(
                'mensaje',
                'Resultado guardado correctamente'
            );
        } catch (InvalidResultException $exc) {
            $app['session']->getFlashBag()->add('error', $exc->getMessage());
        } catch (PersistenceException $exc) {
            $app['session']->getFlashBag()->add('error', $exc->getMessage());
        }
    }

    return $app->redirect('/');
})
    ->bind('homepage_post');

$app->match('/player/update', function (Request $request) use ($app) {
    $token = $app['security.token_storage']->getToken();
    $user = $token->getUser();
    $jugador = $app['jugador_repository']->findById($user->getId());

    /* @var $form Form */
    $form = $app['form.factory']->createBuilder(JugadorUpdateType::class, $jugador)->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $jugador = $form->getData();
        $message = $app['commandBus']->handle(new UpdateJugadorCommand($jugador));
        $app['session']->getFlashBag()->add('mensaje', $message);
        return $app->redirect($request->getUri());
    }

    return $app['twig']->render('player_update.html.twig', [
        'form' => $form->createView(),
    ]);
})
    ->bind('player_update');

$app->get('/loginadmin', function (Request $request) use ($app) {
    return $app['twig']->render('login_admin.html.twig', array(
        'error'         => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ));
});

$app->get('/players/{ligaId}', function ($ligaId) use ($app) {
    $liga = $app['commandBus']->handle(new JugadoresPorLigaCommand(null));
    return $app['twig']->render('players.html.twig', array('liga' => $liga));
})
    ->assert('ligaId', '\d+')
    ->value('ligaId', null)
    ->bind('players');

$app->get('/scores/{ligaId}', function ($ligaId) use ($app) {
    $liga = $app['commandBus']->handle(new ResultadosPorLigaCommand(null));
    return $app['twig']->render('scores.html.twig', array('liga' => $liga));
})
    ->assert('ligaId', '\d+')
    ->value('ligaId', null)
    ->bind('scores');

$app->get('/standings/{ligaId}', function ($ligaId) use ($app) {
    $ligas = $app['commandBus']->handle(new AllLigasCommand(6));
    $puntosGanador = 3;
    $puntosPerdedor = 1;
    $orderBy = [
        'puntos' => 'DESC',
        'difSets' => 'DESC',
        'difJuegos' => 'DESC',
    ];
    $liga = $app['commandBus']->handle(new ClasificacionPorLigaCommand($ligaId, $puntosGanador, $puntosPerdedor, $orderBy));
    return $app['twig']->render('standings.html.twig', [
        'liga' => $liga,
        'ligas' => $ligas
    ]);
})
    ->assert('ligaId', '\d+')
    ->value('ligaId', null)
    ->bind('standings');

$app->get('/ranking', function () use ($app) {
    $limit = 3;
    $puntosGanador = 3;
    $puntosPerdedor = 1;
    $puntosPorCategoria = [
        '1' => 50,
        '2' => 40,
        '3' => 30,
        '4' => 20,
        '5' => 10,
        '6' => 0,
    ];
    $orderBy = [
        'puntos' => 'DESC',
    ];
    $ranking = $app['commandBus']->handle(
        new RankingCommand($limit, $puntosPorCategoria, $puntosGanador, $puntosPerdedor, $orderBy)
    );
    return $app['twig']->render('ranking.html.twig', array('ranking' => $ranking));
})
    ->bind('ranking');

$app->get('/comments', function (Request $request) use ($app) {
    $limit = 50;
    $comentarios = $app['commandBus']->handle(new ComentariosCommand($limit));
    return $app['twig']->render('comments.html.twig', array('comentarios' => $comentarios));
})
    ->bind('comments');

$app->post('/comments', function (Request $request) use ($app) {
    if ($contenido = $request->get('comentario')) {
        $token = $app['security.token_storage']->getToken();
        $user = $token->getUser();
        $error = $app['commandBus']->handle(new AddComentarioCommand($user->getName(), $contenido));
    }
    return $app->redirect('/comments');
})
    ->bind('addcomment');

$app->match('/contact', function (Request $request) use ($app) {
    $contactForm = new ContactForm(null, null, null);
    /* @var $form Form */
    $form = $app['form.factory']->createBuilder(ContactType::class, $contactForm)->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $newContactForm = $form->getData();
        $message = $app['commandBus']->handle(new ContactFormCommand($newContactForm));
        $app['session']->getFlashBag()->add('mensaje', $message);
        return $app->redirect($request->getUri());
    }

    return $app['twig']->render('contact.html.twig', ['form' => $form->createView()]);
})
    ->bind('contact');

$app->post('/freehours', function (Request $request) use ($app) {

    $out = $app['commandBus']->handle(
        new HorasLibresReservaCommand(
            $request->request->get('pista'),
            $request->request->get('fecha')
        )
    );

    return $app->json($out);
})
    ->bind('freehours');

$app->get('/facebook', function () use ($app) {
    return $app['twig']->render('facebook.html.twig', array());
})
    ->bind('facebook');

$app->get('/reglamento', function () use ($app) {
    return $app['twig']->render('reglamento.html.twig', array());
})
    ->bind('reglamento');

$app->error(function (Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/' . $code . '.html.twig',
        'errors/' . substr($code, 0, 2) . 'x.html.twig',
        'errors/' . substr($code, 0, 1) . 'xx.html.twig',
        'errors/default.html.twig',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});
