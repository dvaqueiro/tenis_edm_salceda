<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

//Request::setTrustedProxies(array('127.0.0.1'));

$app->get('/', function () use ($app) {
    //$data = $app['db']->fetchAll('SELECT * FROM comentarios limit 1');
    return $app['twig']->render('homepage.html.twig', array());
})
->bind('homepage');

$app->get('/players', function () use ($app) {
    return $app['twig']->render('players.html.twig', array());
})
->bind('players');

$app->get('/scores', function () use ($app) {
    return $app['twig']->render('scores.html.twig', array());
})
->bind('scores');

$app->get('/standings', function () use ($app) {
    return $app['twig']->render('standings.html.twig', array());
})
->bind('standings');

$app->get('/ranking', function () use ($app) {
    return $app['twig']->render('ranking.html.twig', array());
})
->bind('ranking');

$app->get('/comments', function () use ($app) {
    return $app['twig']->render('comments.html.twig', array());
})
->bind('comments');

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

$app->error(function (\Exception $e, Request $request, $code) use ($app) {
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
