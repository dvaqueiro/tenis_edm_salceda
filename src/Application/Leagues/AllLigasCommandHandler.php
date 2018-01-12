<?php

namespace Application\Leagues;

use Domain\Model\DivisionRepository;
use Domain\Model\LigaRepository;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class AllLigasCommandHandler
{
    /**
     * @var DivisionRepository
     */
    private $divisionRepository;
    private $LigaRepository;

    function __construct(LigaRepository $LigaRepository, DivisionRepository $divisionRepository)
    {
        $this->LigaRepository = $LigaRepository;
        $this->divisionRepository = $divisionRepository;
    }

    public function handle(AllLigasCommand $command)
    {
        $limit = $command->getLimit();

        $ligas = $this->LigaRepository->findLastLimit($limit);

        foreach ($ligas as $liga) {
            /* @var $liga \Domain\Model\Liga */
            $divisiones = $this->divisionRepository->findByLiga($liga->getIdLiga());
            $liga->setDivisiones($divisiones);
        }
        
        return $ligas;
    }
}