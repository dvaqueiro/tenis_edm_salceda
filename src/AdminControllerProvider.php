<?php

use Application\Player\UpdateJugadorCommand;
use Domain\Model\Jugador;
use Infrastructure\Forms\JugadorUpdateAdminType;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\Form\Form;

class AdminControllerProvider implements ControllerProviderInterface
{
    
    public function connect(Application $app): ControllerCollection
    {
        $controllers = $app['controllers_factory'];

        $controllers->get('/', function (Application $app) {
            return $app['twig']->render('admin_home.html.twig', array());
        })->bind('admin');

        $controllers->get('/players', function (Application $app) {
            $jugadores = $app['commandBus']->handle(new \Application\Player\AllPlayersCommand(['ROLE_USER', 'ROLE_ADMIN']));

            return $app['twig']->render('admin_players.html.twig', [
                'jugadores' => $jugadores
            ]);
        })->bind('admin_players');

        $controllers->match('/players/add', function (Application $app) {
            $jugador = new Jugador(null, null, null, null, null, null, null, null);

            /* @var $form Form */
            $form = $app['form.factory']->createBuilder(JugadorUpdateAdminType::class, $jugador)->getForm();

            $request = $app['request_stack']->getCurrentRequest();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $jugador = $form->getData();
                $message = $app['commandBus']->handle(new \Application\Player\AddJugadorCommand($jugador));
                $app['session']->getFlashBag()->add('mensaje', $message);
                return $app->redirect($request->getUri());
            }

            return $app['twig']->render('admin_player_update.html.twig', [
                'form' => $form->createView(),
            ]);
        })->bind('admin_player_add');

        $controllers->match('/players/{idJugador}', function ($idJugador, Application $app) {
            $jugador = $app['jugador_repository']->findById($idJugador);

            /* @var $form Form */
            $form = $app['form.factory']->createBuilder(JugadorUpdateAdminType::class, $jugador)->getForm();

            $request = $app['request_stack']->getCurrentRequest();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $jugador = $form->getData();
                $message = $app['commandBus']->handle(new UpdateJugadorCommand($jugador));
                $app['session']->getFlashBag()->add('mensaje', $message);
                return $app->redirect($request->getUri());
            }

            return $app['twig']->render('admin_player_update.html.twig', [
                'form' => $form->createView(),
            ]);
        })->bind('admin_player_update');

        $controllers->get('/inactive', function (Application $app) {
            $jugadores = $app['commandBus']->handle(new \Application\Player\AllPlayersCommand(['ROLE_NONE']));

            return $app['twig']->render('admin_players.html.twig', [
                'jugadores' => $jugadores
            ]);
        })->bind('admin_players_inactive');

        $controllers->get('/admins', function (Application $app) {
            $jugadores = $app['commandBus']->handle(new \Application\Player\AllPlayersCommand(['ROLE_ADMIN']));

            return $app['twig']->render('admin_players.html.twig', [
                'jugadores' => $jugadores
            ]);
        })->bind('admin_admins');

        $controllers->get('/leagues', function (Application $app) {
            return $app['twig']->render('admin_leagues.html.twig', array());
        })->bind('admin_leagues');

        $controllers->get('/courts', function (Application $app) {
            return $app['twig']->render('admin_courts.html.twig', array());
        })->bind('admin_courts');

        return $controllers;
    }
}
