<?php

namespace Application\Player;

use Domain\Model\DivisionRepository;
use Domain\Model\JugadorRepository;
use Domain\Model\LigaRepository;
use Domain\Model\ResultadoRepository;
use Domain\Model\ResultadosJugador;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class PlayerResultsCommandHandler
{
    private $userRepository;
    private $resultadoRepository;
    private $ligaRepository;
    private $divisionRepository;

    function __construct(JugadorRepository $userRepository, LigaRepository $ligaRepository,
        ResultadoRepository $resultadoRepository, DivisionRepository $divisionRepository)
    {
        $this->userRepository = $userRepository;
        $this->resultadoRepository = $resultadoRepository;
        $this->ligaRepository = $ligaRepository;
        $this->divisionRepository = $divisionRepository;
    }

    function handle(PlayerResultsCommand $command)
    {
        $jugadorId = $command->getJugadorId();
        $liga = $this->ligaRepository->findByIdOrLast(null);
        $jugador = $this->userRepository->findById($jugadorId);
        $division = $this->divisionRepository->findByLigaAndJugador($liga->getIdLiga(), $jugadorId);
        $rivales = $this->userRepository->findRivales($liga->getIdLiga(), $jugadorId);
        $resultados = $this->resultadoRepository->findByLigaAndJugador($liga->getIdLiga(), $jugadorId);

        return new ResultadosJugador($division, $jugador, $rivales,
            new \Doctrine\Common\Collections\ArrayCollection($resultados->slice(0, 3)));
    }
}