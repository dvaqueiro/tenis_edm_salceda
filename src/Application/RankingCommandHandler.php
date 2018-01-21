<?php


namespace Application;

use Domain\Model\DivisionRepository;
use Domain\Model\JugadorRepository;
use Domain\Model\LigaRepository;
use Domain\Model\Ranking;
use Domain\Model\Resultado\ResultadoRepository;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class RankingCommandHandler
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
    
    public function handle(RankingCommand $command)
    {
        $ligas = $this->ligaRepository->findLastLimit($command->getLimit());

        foreach ($ligas as $liga) {
            $divisiones = $this->divisionRepositorio->findByLiga($liga->getIdLiga());
            foreach ($divisiones as $division) {
                $resultados = $this->resultadoRepository->findByDivision($division->getIdDivision());
                $division->setResultados($resultados);
                $participantes = $this->jugadorRepository->findByDivision($division->getIdDivision());
                $division->setParticipantes($participantes);
                $liga->addDivision($division);
            }
        }

        return new Ranking(
            $ligas,
            $command->getPuntosPorCategoria(),
            $command->getPuntosGanador(),
            $command->getPuntosPerdedor(),
            $command->getOrderBy()
        );
    }
}