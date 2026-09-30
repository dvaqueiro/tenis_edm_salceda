<?php

use Application\AddResultadoCommad;
use Application\DeleteResultadoCommand;
use Application\Leagues\CreateLeagueCommand;
use Application\Leagues\InvalidLeagueException;
use Application\Leagues\NewLeagueDraftCommand;
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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Csrf\CsrfToken;

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

        $controllers->post('/players/delete/{idJugador}', function ($idJugador, Application $app) {
            try {
                $app['commandBus']->handle(new \Application\Player\DeleteJugadorCommand($idJugador));
                $app['session']->getFlashBag()->add('mensaje', "se ha eliminado al jugador correctamente.");
            } catch (PersistenceException $ex) {
                $app['session']->getFlashBag()->add('error', "Se ha producido un error al intentar eliminar al jugador");
            }

            return $app->redirect('/admin/players');
        })->bind('admin_player_delete')
            ->assert('idJugador', '\d+');

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
            ->assert('idJugador', '\d+');

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

        $soloAdmin = function (Request $request, Application $app) {
            if (!$app['security.authorization_checker']->isGranted('ROLE_ADMIN')) {
                throw new AccessDeniedHttpException('Solo los administradores pueden crear ligas.');
            }
        };

        $controllers->get('/leagues/add', function (Request $request, Application $app) {
            $borrador = $app['commandBus']->handle(new NewLeagueDraftCommand(
                $request->query->getInt('base') ?: null,
                3,
                1,
                [
                    'puntos' => 'DESC',
                    'difSets' => 'DESC',
                    'difJuegos' => 'DESC',
                ]
            ));

            return $app['twig']->render('admin_league_add.html.twig', [
                'borrador' => $borrador,
                'borradorJson' => json_encode($borrador, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE),
                'csrfToken' => $app['csrf.token_manager']->getToken('league_add')->getValue(),
            ]);
        })->bind('admin_league_add')
            ->before($soloAdmin);

        $controllers->post('/leagues/add', function (Request $request, Application $app) {
            $token = new CsrfToken('league_add', $request->headers->get('X-CSRF-Token'));
            if (!$app['csrf.token_manager']->isTokenValid($token)) {
                return new JsonResponse(['ok' => false, 'errors' => [
                    'La sesión ha caducado. Recarga la página: tu borrador se conserva en este navegador.'
                ]], 403);
            }

            $datos = json_decode($request->getContent(), true);
            if (!is_array($datos)) {
                return new JsonResponse(['ok' => false, 'errors' => ['Datos no válidos.']], 400);
            }

            try {
                $creada = $app['commandBus']->handle(new CreateLeagueCommand(
                    isset($datos['nombre']) ? $datos['nombre'] : '',
                    isset($datos['grupos']) ? $datos['grupos'] : []
                ));
            } catch (InvalidLeagueException $ex) {
                return new JsonResponse(['ok' => false, 'errors' => $ex->getErrors()], 422);
            } catch (\Exception $ex) {
                $app['logger'] && $app['logger']->error('Error al crear la liga: ' . $ex->getMessage());
                return new JsonResponse(['ok' => false, 'errors' => [
                    'No se ha podido guardar la liga. No se ha creado nada; inténtalo de nuevo.'
                ]], 500);
            }

            $mensaje = 'Liga «' . trim($datos['nombre']) . '» creada correctamente.';
            if ($creada['reactivados'] > 0) {
                $mensaje .= " Se han reactivado {$creada['reactivados']} jugadores que estaban inactivos.";
            }
            $app['session']->getFlashBag()->add('mensaje', $mensaje);

            return new JsonResponse([
                'ok' => true,
                'idLiga' => $creada['idLiga'],
                'redirect' => $app['url_generator']->generate('admin_leagues'),
            ]);
        })->bind('admin_league_create')
            ->before($soloAdmin);

        $controllers->get('/leagues/{idLiga}/division/{idDivision}', function ($idLiga, $idDivision, Application $app) {
            $puntosGanador = 3;
            $puntosPerdedor = 1;
            $orderBy = [
                'puntos' => 'DESC',
                'difSets' => 'DESC',
                'difJuegos' => 'DESC',
            ];
            $liga = $app['commandBus']->handle(new \Application\AllAboutDivisionCommand(
                $idLiga,
                $idDivision,
                $puntosGanador,
                $puntosPerdedor,
                $orderBy
            ));
            //Symfony\Component\VarDumper\VarDumper::dump($liga);

            return $app['twig']->render('admin_results.html.twig', [
                'liga' => $liga,
                'idDivision' => $idDivision
            ]);
        })->bind('admin_results')
            ->assert('idLiga', '\d+')
            ->assert('idDivision', '\d+');

        $controllers->get('/leagues/{idLiga}/division/{idDivision}/player/{idPlayer}', function ($idLiga, $idDivision, $idPlayer, Application $app) {

            $handler = new PlayerResultsCommandHandler(
                $app['jugador_repository'],
                $app['liga_repository'],
                $app['resultado_repository'],
                $app['division_repository']
            );
            $resultadosJugador = $handler->handle(new PlayerResultsCommand($idPlayer, $idLiga));

            return $app['twig']->render('admin_player_results.html.twig', [
                'resultadosJugador' => $resultadosJugador
            ]);
        })->bind('admin_player_results')
            ->assert('idLiga', '\d+')
            ->assert('idDivision', '\d+')
            ->assert('idPlayer', '\d+');

        $controllers->post('/leagues/{idLiga}/division/{idDivision}/player/{idPlayer}', function ($idLiga, $idDivision, $idPlayer, Application $app) {
            $request = $app['request_stack']->getCurrentRequest();
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
            return $app->redirect($request->getUri());
        })->bind('admin_player_add_results')
            ->assert('idLiga', '\d+')
            ->assert('idDivision', '\d+')
            ->assert('idPlayer', '\d+');

        $controllers->delete('/result/{idResultado}', function ($idResultado, Application $app) {
            $ok = $app['commandBus']->handle(new DeleteResultadoCommand($idResultado));

            return new JsonResponse(['ok' => $ok]);
        })->bind('admin_result_delete')
            ->assert('idResultado', '\d+');


        return $controllers;
    }
}
