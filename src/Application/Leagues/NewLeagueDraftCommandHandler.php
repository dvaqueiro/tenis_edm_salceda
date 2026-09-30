<?php

namespace Application\Leagues;

use Domain\Model\Clasificacion;
use Domain\Model\Division;
use Domain\Model\DivisionRepository;
use Domain\Model\Jugador;
use Domain\Model\JugadorRepository;
use Domain\Model\LigaRepository;
use Domain\Model\Resultado\ResultadoRepository;

/**
 * Devuelve la clasificación final de cada grupo de la liga base, la lista de
 * jugadores y un nombre sugerido para la nueva liga. Con esto la pantalla de
 * administración propone las tablas de la nueva liga.
 */
class NewLeagueDraftCommandHandler
{
    const PERIODOS = ['enero-marzo', 'mayo-julio', 'septiembre-noviembre'];

    private $ligaRepository;
    private $divisionRepository;
    private $resultadoRepository;
    private $jugadorRepository;

    function __construct(LigaRepository $ligaRepository, DivisionRepository $divisionRepository,
        ResultadoRepository $resultadoRepository, JugadorRepository $jugadorRepository)
    {
        $this->ligaRepository = $ligaRepository;
        $this->divisionRepository = $divisionRepository;
        $this->resultadoRepository = $resultadoRepository;
        $this->jugadorRepository = $jugadorRepository;
    }

    public function handle(NewLeagueDraftCommand $command)
    {
        $liga = $this->ligaRepository->findByIdOrLast($command->getIdLigaBase());

        $divisiones = [];
        foreach ($this->divisionRepository->findByLiga($liga->getIdLiga()) as $division) {
            $divisiones[] = $this->divisionConClasificacion($division, $command);
        }
        usort($divisiones, function ($a, $b) {
            return [$a['categoria'], $a['nombre']] <=> [$b['categoria'], $b['nombre']];
        });

        $jugadores = [];
        foreach ($this->jugadorRepository->findAll() as $jugador) {
            /* @var $jugador Jugador */
            $jugadores[] = [
                'id' => (int) $jugador->getId(),
                'nombre' => $jugador->getNombre(),
                'activo' => $jugador->getRoles() !== 'ROLE_NONE',
            ];
        }
        usort($jugadores, function ($a, $b) {
            return strcasecmp($a['nombre'], $b['nombre']);
        });

        $ligas = [];
        foreach ($this->ligaRepository->findLastLimit($command->getLimiteLigas()) as $otraLiga) {
            $ligas[] = ['id' => (int) $otraLiga->getIdLiga(), 'nombre' => $otraLiga->getNombreLiga()];
        }

        return [
            'base' => ['id' => (int) $liga->getIdLiga(), 'nombre' => $liga->getNombreLiga()],
            'nombreSugerido' => self::siguienteNombre($liga->getNombreLiga()),
            'divisiones' => $divisiones,
            'jugadores' => $jugadores,
            'ligas' => $ligas,
        ];
    }

    private function divisionConClasificacion(Division $division, NewLeagueDraftCommand $command)
    {
        $participantes = $this->jugadorRepository->findByDivision($division->getIdDivision());
        $resultados = $this->resultadoRepository->findByDivision($division->getIdDivision());
        $clasificacion = new Clasificacion(
            $participantes,
            $resultados,
            $command->getPuntosGanador(),
            $command->getPuntosPerdedor(),
            $command->getOrder()
        );

        $filas = [];
        $posicion = 0;
        foreach ($clasificacion->getAgregados() as $idJugador => $agregado) {
            $filas[] = [
                'id' => (int) $idJugador,
                'nombre' => $agregado['nombre'],
                'posicion' => ++$posicion,
                'puntos' => $agregado['puntos'],
                'partidos' => $agregado['partidos'],
                'win' => $agregado['win'],
                'lost' => $agregado['lost'],
                'difSets' => $agregado['difSets'],
                'difJuegos' => $agregado['difJuegos'],
            ];
        }

        return [
            'id' => (int) $division->getIdDivision(),
            'nombre' => $division->getNombre(),
            'categoria' => (int) $division->getCategoria(),
            'clasificacion' => $filas,
        ];
    }

    /**
     * "Liga mayo-julio 2026" => "Liga septiembre-noviembre 2026"
     * "Liga septiembre-noviembre 2026" => "Liga enero-marzo 2027"
     */
    public static function siguienteNombre($nombre)
    {
        $periodos = implode('|', self::PERIODOS);
        if (!preg_match('/^(.*?)(' . $periodos . ')\s+(\d{4})\s*$/u', $nombre, $partes)) {
            return '';
        }

        $indice = array_search($partes[2], self::PERIODOS);
        $anio = (int) $partes[3];
        $siguiente = ($indice + 1) % count(self::PERIODOS);
        if ($siguiente === 0) {
            $anio++;
        }

        return $partes[1] . self::PERIODOS[$siguiente] . ' ' . $anio;
    }
}
