<?php

namespace Application;

use Domain\Model\Clasificacion;
use Domain\Model\DivisionRepository;
use Domain\Model\LigaRepository;
use Domain\Model\ResultadoRepository;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class ClasificacionPorLigaHandler
{
    private $ligaRepository;
    private $divisionRepositorio;
    private $resultadoRepository;

    function __construct(LigaRepository $ligaRepository, DivisionRepository $divisionRepositorio,
        ResultadoRepository $resultadoRepository)
    {
        $this->ligaRepository = $ligaRepository;
        $this->divisionRepositorio = $divisionRepositorio;
        $this->resultadoRepository = $resultadoRepository;
    }

    public function handle(ClasificacionPorLigaCommand $command)
    {
        $ligaId = $command->getIdLiga();
        $liga = $this->ligaRepository->findByIdOrLast($ligaId);

        $divisiones = $this->divisionRepositorio->findByLiga($liga->getIdLiga());
        foreach ($divisiones as $division) {
            $resultados = $this->resultadoRepository->findByDivision($division->getIdDivision());
            $division->setClasificacion(
                new Clasificacion(
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