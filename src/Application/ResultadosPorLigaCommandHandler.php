<?php

namespace Application;

use Domain\Model\DivisionRepository;
use Domain\Model\LigaRepository;
use Domain\Model\Resultado\ResultadoRepository;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class ResultadosPorLigaCommandHandler
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

    public function handle(ResultadosPorLigaCommand $command)
    {
        $ligaId = $command->getIdLiga();
        $liga = $this->ligaRepository->findByIdOrLast($ligaId);

        $divisiones = $this->divisionRepositorio->findByLiga($liga->getIdLiga());
        foreach ($divisiones as $division) {
            $resultados = $this->resultadoRepository->findByDivision($division->getIdDivision());
            $division->setResultados($resultados);
            $liga->addDivision($division);
        }

        return $liga;
    }
}