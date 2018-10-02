<?php

use Application\AddResultadoCommad;
use Application\DeleteResultadoCommand;
use Application\Player\UpdateJugadorCommand;
use Domain\Model\Jugador;
use Domain\Model\PersistenceException;
use Domain\Model\Resultado\InvalidResultException;
use Infrastructure\Forms\JugadorUpdateAdminType;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\Form\Form;
use Application\Player\PlayerResultsCommand;
use Application\Player\PlayerResultsCommandHandler;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdminControllerProvider implements ControllerProviderInterface
{
    
    public function connect(Application $app): ControllerCollection
    {
        $controllers = $app['controllers_factory'];

        $controllers->get('/', function (Application $app) {
            return $app['twig']->render('admin_home.html.twig', array());
        })->bind('admin');

        $controllers->get('/players', function (Application $app) {
            $jugadores = $app['commandBus']->handle(new \Application\Player\AllPlayersCommand(['ROLE_USER','ROLE_ADMIN']));

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

        $controllers->post('/players/delete/{idJugador}', function ($idJugador, Application $app) {
            try {
                $app['commandBus']->handle(new \Application\Player\DeleteJugadorCommand($idJugador));
                $app['session']->getFlashBag()->add('mensaje', "se ha eliminado al jugador correctamente.");
            } catch (PersistenceException $ex) {
                $app['session']->getFlashBag()->add('error', "Se ha producido un error al intentar eliminar al jugador");
            }
            
            return $app->redirect('/admin/players');
        })->bind('admin_player_delete')
            ->assert('idJugador','\d+');

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
        })->bind('admin_player_update')
            ->assert('idJugador','\d+');

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
            $limit = 20;
            $ligas = $app['commandBus']->handle(new \Application\Leagues\AllLigasCommand($limit));
            return $app['twig']->render('admin_leagues.html.twig', [
                'ligas' => $ligas,
            ]);
        })->bind('admin_leagues');

        $controllers->get('/leagues/{idLiga}/division/{idDivision}', function ($idLiga, $idDivision, Application $app) {
            $puntosGanador = 3;
            $puntosPerdedor = 1;
            $orderBy = [
                'puntos' => 'DESC',
                'difSets' => 'DESC',
                'difJuegos' => 'DESC',
            ];
            $liga = $app['commandBus']->handle(new \Application\AllAboutDivisionCommand($idLiga, $idDivision,
                $puntosGanador, $puntosPerdedor, $orderBy));
            //Symfony\Component\VarDumper\VarDumper::dump($liga);

            return $app['twig']->render('admin_results.html.twig', [
                'liga' => $liga,
                'idDivision' => $idDivision
            ]);
        })->bind('admin_results')
        ->assert('idLiga','\d+')
        ->assert('idDivision','\d+');

        $controllers->get('/leagues/{idLiga}/division/{idDivision}/player/{idPlayer}', function ($idLiga, $idDivision, $idPlayer, Application $app) {       

            $handler = new PlayerResultsCommandHandler($app['jugador_repository'], $app['liga_repository'], 
                $app['resultado_repository'], $app['division_repository']);
            $resultadosJugador = $handler->handle(new PlayerResultsCommand($idPlayer));
            //Symfony\Component\VarDumper\VarDumper::dump($resultadosJugador);

            return $app['twig']->render('admin_player_results.html.twig', [
                'resultadosJugador' => $resultadosJugador
            ]);
        })->bind('admin_player_results')
          ->assert('idLiga','\d+')
          ->assert('idDivision','\d+')
          ->assert('idPlayer','\d+');

        $controllers->post('/leagues/{idLiga}/division/{idDivision}/player/{idPlayer}', function ($idLiga, $idDivision, $idPlayer, Application $app) 
        {
            $request = $app['request_stack']->getCurrentRequest();
            if($request->get('form')) {
                try {
                    $formData = $request->get('form');
                    $app['commandBus']->handle(
                        new AddResultadoCommad($formData)
                    );
                    $app['session']->getFlashBag()->add('mensaje',
                        'Resultado guardado correctamente');
                } catch (InvalidResultException $exc) {
                    $app['session']->getFlashBag()->add('error', $exc->getMessage());
                } catch (PersistenceException $exc) {
                    $app['session']->getFlashBag()->add('error', $exc->getMessage());
                }
            }
            return $app->redirect($request->getUri());
        })->bind('admin_player_add_results')
          ->assert('idLiga','\d+')
          ->assert('idDivision','\d+')
          ->assert('idPlayer','\d+');


        $controllers->get('/courts', function (Application $app) {
            $limit = 100;
            $orderBy = [
                'fecha' => 'DESC',
                'hora' => 'ASC'
            ];
            $reservas = $app['commandBus']->handle(new \Application\CourtBooking\AllBookingsCommand($limit, $orderBy));

            $pistas = [
                1 => 'Pabellón parderrubias',
                2 => 'Pista exterior'
            ];

            return $app['twig']->render('admin_courts.html.twig', [
                'reservas' => $reservas,
                'pistas' => $pistas,
            ]);
        })->bind('admin_courts');

        $controllers->delete('/result/{idResultado}', function ($idResultado, Application $app) {
            $ok = $app['commandBus']->handle(new DeleteResultadoCommand($idResultado));
            
            return new JsonResponse(['ok' => $ok]);
        })->bind('admin_result_delete')
            ->assert('idResultado','\d+');


        return $controllers;
    }
}
