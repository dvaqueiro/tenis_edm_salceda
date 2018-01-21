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
class AllAboutDivisionCommandHandler
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

    public function handle(AllAboutDivisionCommand $command)
    {
        $ligaId = $command->getIdLiga();
        $divisionId = $command->getIdDivision();
        $liga = $this->ligaRepository->findByIdOrLast($ligaId);
        $division = $this->divisionRepositorio->findById($divisionId);
        $participantes = $this->jugadorRepository->findByDivision($division->getIdDivision());

        $resultados = $this->resultadoRepository->findByDivision($division->getIdDivision());
        $division->setResultados($resultados);
        $division->setClasificacion(
            new Clasificacion(
                $participantes,
                $resultados,
                $command->getPuntosGanador(),
                $command->getPuntosPerdedor(),
                $command->getOrder())
        );
        $liga->addDivision($division);

        return $liga;
    }
}