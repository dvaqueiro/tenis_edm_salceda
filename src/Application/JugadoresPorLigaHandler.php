<?php

namespace Application;

use Domain\Model\DivisionRepository;
use Domain\Model\JugadorRepository;
use Domain\Model\LigaRepository;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class JugadoresPorLigaHandler
{
    /**
     * @var JugadorRepository
     */
    private $jugadorRespositorio;

    /**
     * @var DivisionRepository
     */
    private $divisionRepositorio;

    /**
     * @var LigaRepository
     */
    private $LigaRepository;

    function __construct(LigaRepository $LigaRepository, DivisionRepository $divisionRepositorio,
        JugadorRepository $jugadorRespositorio)
    {
        $this->LigaRepository = $LigaRepository;
        $this->divisionRepositorio = $divisionRepositorio;
        $this->jugadorRespositorio = $jugadorRespositorio;
    }

    public function handle(JugadoresPorLigaCommand $command)
    {
        $ligaId = $command->getLigaId();
        $liga = $this->LigaRepository->findByIdOrLast($ligaId);

        $divisiones = $this->divisionRepositorio->findByLiga($liga->getIdLiga());
        foreach ($divisiones as $division) {
            $jugadores = $this->jugadorRespositorio->findByDivision($division->getIdDivision());
            $division->setParticipantes($jugadores);
            $liga->addDivision($division);
        }

        return $liga;
    }
}