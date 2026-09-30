<?php

namespace Application\Leagues;

use Domain\Model\DivisionRepository;
use Domain\Model\Jugador;
use Domain\Model\JugadorRepository;
use Domain\Model\LigaRepository;

/**
 * Valida y guarda una liga nueva. El bus de comandos la ejecuta dentro de una
 * transacción: si algo falla no se guarda nada.
 */
class CreateLeagueCommandHandler
{
    const MAX_NOMBRE = 150;

    private $ligaRepository;
    private $divisionRepository;
    private $jugadorRepository;

    function __construct(LigaRepository $ligaRepository, DivisionRepository $divisionRepository,
        JugadorRepository $jugadorRepository)
    {
        $this->ligaRepository = $ligaRepository;
        $this->divisionRepository = $divisionRepository;
        $this->jugadorRepository = $jugadorRepository;
    }

    /**
     * @return array ['idLiga' => int, 'reactivados' => int]
     * @throws InvalidLeagueException
     */
    public function handle(CreateLeagueCommand $command)
    {
        $jugadores = [];
        foreach ($this->jugadorRepository->findAll() as $jugador) {
            /* @var $jugador Jugador */
            $jugadores[(int) $jugador->getId()] = $jugador;
        }

        $nombre = trim((string) $command->getNombre());
        $grupos = $this->validar($nombre, $command->getGrupos(), $jugadores);

        $idLiga = $this->ligaRepository->add($nombre);
        $inactivos = [];
        foreach ($grupos as $grupo) {
            $idDivision = $this->divisionRepository->add($idLiga, $grupo['nombre'], $grupo['categoria']);
            foreach ($grupo['jugadores'] as $idJugador) {
                $this->divisionRepository->addJugador($idDivision, $idJugador);
                if ($jugadores[$idJugador]->getRoles() === 'ROLE_NONE') {
                    $inactivos[] = $idJugador;
                }
            }
        }
        $reactivados = $this->jugadorRepository->activate($inactivos);

        return ['idLiga' => $idLiga, 'reactivados' => $reactivados];
    }

    private function validar($nombre, $grupos, array $jugadores)
    {
        $errores = [];

        if ($nombre === '') {
            $errores[] = 'Indica el nombre de la liga.';
        } elseif (mb_strlen($nombre) > self::MAX_NOMBRE) {
            $errores[] = 'El nombre de la liga no puede superar ' . self::MAX_NOMBRE . ' caracteres.';
        } elseif ($this->ligaRepository->existsByNombre($nombre)) {
            $errores[] = "Ya existe una liga llamada «{$nombre}».";
        }

        if (!is_array($grupos) || empty($grupos)) {
            $errores[] = 'La liga necesita al menos un grupo.';
            throw new InvalidLeagueException($errores);
        }

        $limpios = [];
        $nombresGrupo = [];
        $grupoDeJugador = [];
        foreach ($grupos as $grupo) {
            $nombreGrupo = trim((string) (isset($grupo['nombre']) ? $grupo['nombre'] : ''));
            $categoria = isset($grupo['categoria']) ? $grupo['categoria'] : null;
            $ids = isset($grupo['jugadores']) && is_array($grupo['jugadores']) ? $grupo['jugadores'] : [];
            $etiqueta = $nombreGrupo !== '' ? "«{$nombreGrupo}»" : 'sin nombre';

            if ($nombreGrupo === '') {
                $errores[] = 'Hay un grupo sin nombre.';
            } elseif (mb_strlen($nombreGrupo) > self::MAX_NOMBRE) {
                $errores[] = "El nombre del grupo {$etiqueta} es demasiado largo.";
            } elseif (isset($nombresGrupo[mb_strtolower($nombreGrupo)])) {
                $errores[] = "Hay dos grupos llamados {$etiqueta}.";
            }
            $nombresGrupo[mb_strtolower($nombreGrupo)] = true;

            if (filter_var($categoria, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 99]]) === false) {
                $errores[] = "La categoría del grupo {$etiqueta} no es válida.";
            }

            if (empty($ids)) {
                $errores[] = "El grupo {$etiqueta} no tiene jugadores.";
            }

            $idsLimpios = [];
            foreach ($ids as $id) {
                $id = filter_var($id, FILTER_VALIDATE_INT);
                if ($id === false || !isset($jugadores[$id])) {
                    $errores[] = "El grupo {$etiqueta} contiene un jugador que no existe.";
                    continue;
                }
                if (isset($grupoDeJugador[$id])) {
                    $errores[] = "{$jugadores[$id]->getNombre()} está en dos grupos ({$grupoDeJugador[$id]} y {$etiqueta}).";
                    continue;
                }
                $grupoDeJugador[$id] = $etiqueta;
                $idsLimpios[] = $id;
            }

            $limpios[] = ['nombre' => $nombreGrupo, 'categoria' => (int) $categoria, 'jugadores' => $idsLimpios];
        }

        if (!empty($errores)) {
            throw new InvalidLeagueException(array_values(array_unique($errores)));
        }

        return $limpios;
    }
}
