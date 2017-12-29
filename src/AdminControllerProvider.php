<?php

use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;

class AdminControllerProvider implements ControllerProviderInterface
{
    
    public function connect(Application $app): ControllerCollection
    {
        $controllers = $app['controllers_factory'];

        $controllers->get('/', function (Application $app) {
            return $app['twig']->render('admin_home.html.twig', array());
        })->bind('admin');

        $controllers->get('/players', function (Application $app) {
            $jugadorRepository = $app['jugador_repository'];
            $jugadores = $jugadorRepository->findAll();

            Symfony\Component\VarDumper\VarDumper::dump($jugadores);

            return $app['twig']->render('admin_players.html.twig', [
                'jugadores' => $jugadores
            ]);
        })->bind('admin_players');

        $controllers->get('/leagues', function (Application $app) {
            return $app['twig']->render('admin_leagues.html.twig', array());
        })->bind('admin_leagues');

        $controllers->get('/courts', function (Application $app) {
            return $app['twig']->render('admin_courts.html.twig', array());
        })->bind('admin_courts');

        return $controllers;
    }
}
