<?php

namespace Application;

use Domain\Model\DivisionRepository;
use Domain\Model\LigaRepository;
use Domain\Model\ResultadoRepository;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class ResultadosPorLigaCommandHandler
{
    private $LigaRepository;
    private $divisionRepositorio;
    private $resultadoRepository;

    function __construct(LigaRepository $LigaRepository, DivisionRepository $divisionRepositorio, 
        ResultadoRepository $resultadoRepository)
    {
        $this->LigaRepository = $LigaRepository;
        $this->divisionRepositorio = $divisionRepositorio;
        $this->resultadoRepository = $resultadoRepository;
    }

    public function handle(ResultadosPorLigaCommand $command)
    {
        $ligaId = $command->getIdLiga();
        $liga = $this->LigaRepository->findByIdOrLast($ligaId);

        $divisiones = $this->divisionRepositorio->findByLiga($liga->getIdLiga());
        foreach ($divisiones as $division) {
            $resultados = $this->resultadoRepository->findByDivision($division->getIdDivision());
            $division->setResultados($resultados);
            $liga->addDivision($division);
        }

        return $liga;
    }
}