<?php

namespace Application;

use Domain\Model\Clasificacion;
use Domain\Model\DivisionRepository;
use Domain\Model\LigaRepository;
use Domain\Model\Resultado\ResultadoRepository;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class AllAboutDivisionCommandHandler
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

    public function handle(AllAboutDivisionCommand $command)
    {
        $ligaId = $command->getIdLiga();
        $divisionId = $command->getIdDivision();
        $liga = $this->ligaRepository->findByIdOrLast($ligaId);
        $division = $this->divisionRepositorio->findById($divisionId);

        $resultados = $this->resultadoRepository->findByDivision($division->getIdDivision());
        $division->setResultados($resultados);
        $division->setClasificacion(
            new Clasificacion(
                $resultados,
                $command->getPuntosGanador(),
                $command->getPuntosPerdedor(),
                $command->getOrder())
        );
        $liga->addDivision($division);

        return $liga;
    }
}