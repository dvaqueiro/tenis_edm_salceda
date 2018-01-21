<?php

namespace Application;

use Domain\Model\Clasificacion;
use Domain\Model\DivisionRepository;
use Domain\Model\JugadorRepository;
use Domain\Model\LigaRepository;
use Domain\Model\Resultado\ResultadoRepository;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class ClasificacionPorLigaHandler
{
    /**
     * @var JugadorRepository
     */
    private $jugadorRepository;
    private $ligaRepository;
    private $divisionRepositorio;
    private $resultadoRepository;

    function __construct(LigaRepository $ligaRepository, DivisionRepository $divisionRepositorio,
        ResultadoRepository $resultadoRepository, JugadorRepository $jugadorRepository)
    {
        $this->ligaRepository = $ligaRepository;
        $this->divisionRepositorio = $divisionRepositorio;
        $this->resultadoRepository = $resultadoRepository;
        $this->jugadorRepository = $jugadorRepository;
    }

    public function handle(ClasificacionPorLigaCommand $command)
    {
        $ligaId = $command->getIdLiga();
        $liga = $this->ligaRepository->findByIdOrLast($ligaId);

        $divisiones = $this->divisionRepositorio->findByLiga($liga->getIdLiga());

        foreach ($divisiones as $division) {
            $participantes = $this->jugadorRepository->findByDivision($division->getIdDivision());
            $resultados = $this->resultadoRepository->findByDivision($division->getIdDivision());
            $division->setClasificacion(
                new Clasificacion(
                    $participantes,
                    $resultados,
                    $command->getPuntosGanador(),
                    $command->getPuntosPerdedor(),
                    $command->getOrder())
            );
            $liga->addDivision($division);
        }

        return $liga;
    }
}