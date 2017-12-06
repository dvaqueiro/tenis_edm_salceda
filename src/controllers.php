<?php

use Application\AddComentarioCommand;
use Application\CourtBooking\AddReservaCommand;
use Application\ClasificacionPorLigaCommand;
use Application\ComentariosCommand;
use Application\ContactFormCommand;
use Application\CourtBooking\HorasLibresReservaCommand;
use Application\JugadoresPorLigaCommand;
use Application\RankingCommand;
use Application\RegisterJugadorCommand;
use Application\ResultadosPorLigaCommand;
use Domain\Model\ContactForm;
use Domain\Model\Jugador;
use Domain\Model\Reserva;
use Infrastructure\Forms\BookingType;
use Infrastructure\Forms\ContactType;
use Infrastructure\Forms\JugadorType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

//Request::setTrustedProxies(array('127.0.0.1'));

$app->get('/', function () use ($app) {
    return $app['twig']->render('homepage.html.twig', array());
})
->bind('homepage');

$app->match('/login', function(Request $request) use ($app) {
    $jugador = new Jugador(null, null, null, null, null, null, null);
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

$app->get('/loginadmin', function(Request $request) use ($app) {
    return $app['twig']->render('login_admin.html.twig', array(
        'error'         => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ));
});

$app->get('/admin', function () use ($app) {
    return $app['twig']->render('admin.html.twig', array());
})
->bind('admin');

$app->get('/players/{ligaId}', function ($ligaId) use ($app) {
    $liga = $app['commandBus']->handle(new JugadoresPorLigaCommand(null));
    return $app['twig']->render('players.html.twig', array('liga' => $liga));
})
->value('ligaId', null)
->bind('players');

$app->get('/scores/{ligaId}', function ($ligaId) use ($app) {
    $liga = $app['commandBus']->handle(new ResultadosPorLigaCommand(null));
    return $app['twig']->render('scores.html.twig', array('liga' => $liga));
})
->value('ligaId', null)
->bind('scores');

$app->get('/standings/{ligaId}', function ($ligaId) use ($app) {
    $puntosGanador = 3;
    $puntosPerdedor = 1;
    $orderBy = [
        'puntos' => 'DESC',
        'difSets' => 'DESC',
        'difJuegos' => 'DESC',
    ];
    $liga = $app['commandBus']->handle(new ClasificacionPorLigaCommand($ligaId, $puntosGanador, $puntosPerdedor, $orderBy));
    return $app['twig']->render('standings.html.twig', array('liga' => $liga));
})
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
    if($contenido = $request->get('comentario')) {
        $nombreUsuario = "Daniel Vaqueiro Crispín";
        $error = $app['commandBus']->handle(new AddComentarioCommand($nombreUsuario, $contenido));
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

$app->match('/courts', function (Request $request) use ($app) {
    if(!$app['booking_checker']->checkInDate()) {
        $app['session']->getFlashBag()->add('error', "Debe reservar la pista antes del viernes a las 12h");
        return $app->redirect('/');
    }

    $token = $app['security.token_storage']->getToken();
    $user = $token->getUser();

    $newReserva = new Reserva(null, $user->getId(), null, null, null);
    /* @var $form Form */
    $form = $app['form.factory']->createBuilder(BookingType::class, $newReserva)->getForm();
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $newReserva = $form->getData();
        $message = $app['commandBus']->handle(new AddReservaCommand($newReserva));
        $app['session']->getFlashBag()->add('mensaje', $message);
        return $app->redirect($request->getUri());
    }

    return $app['twig']->render('courts.html.twig', ['form' => $form->createView()]);
})
->bind('courts');

$app->post('/freehours', function (Request $request) use ($app) {

    $out = $app['commandBus']->handle(new HorasLibresReservaCommand(
        $request->request->get('pista'), 
        $request->request->get('fecha'))
    );

    return $app->json($out);
})
->bind('freehours');


$app->get('/facebook', function () use ($app) {
    return $app['twig']->render('facebook.html.twig', array());
})
->bind('facebook');

$app->error(function (Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/'.$code.'.html.twig',
        'errors/'.substr($code, 0, 2).'x.html.twig',
        'errors/'.substr($code, 0, 1).'xx.html.twig',
        'errors/default.html.twig',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});
