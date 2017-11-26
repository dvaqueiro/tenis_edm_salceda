<?php

use Application\AddComentarioCommand;
use Application\ClasificacionPorLigaCommand;
use Application\ComentariosCommand;
use Application\JugadoresPorLigaCommand;
use Application\RankingCommand;
use Application\ResultadosPorLigaCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

//Request::setTrustedProxies(array('127.0.0.1'));

$app->get('/', function () use ($app) {
    return $app['twig']->render('homepage.html.twig', array());
})
->bind('homepage');

$app->get('/players', function () use ($app) {
    //TODO: Solo registrados
    $liga = $app['commandBus']->handle(new JugadoresPorLigaCommand(null));
    return $app['twig']->render('players.html.twig', array('liga' => $liga));
})
->bind('players');

$app->get('/scores', function () use ($app) {
    $liga = $app['commandBus']->handle(new ResultadosPorLigaCommand(null));
    return $app['twig']->render('scores.html.twig', array('liga' => $liga));
})
->bind('scores');

$app->get('/standings', function () use ($app) {
    $puntosGanador = 3;
    $puntosPerdedor = 1;
    $orderBy = [
        'puntos' => 'DESC',
        'difSets' => 'DESC',
        'difJuegos' => 'DESC',
    ];
    $liga = $app['commandBus']->handle(new ClasificacionPorLigaCommand(null, $puntosGanador, $puntosPerdedor, $orderBy));
    return $app['twig']->render('standings.html.twig', array('liga' => $liga));
})
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

$app->get('/contact', function () use ($app) {
    return $app['twig']->render('contact.html.twig', array());
})
->bind('contact');

$app->get('/stadium', function () use ($app) {
    return $app['twig']->render('stadium.html.twig', array());
})
->bind('stadium');

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