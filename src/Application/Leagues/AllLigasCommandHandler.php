<?php

namespace Application\Leagues;

use Domain\Model\LigaRepository;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class AllLigasCommandHandler
{
    private $LigaRepository;

    function __construct(LigaRepository $LigaRepository)
    {
        $this->LigaRepository = $LigaRepository;
    }

    public function handle(AllLigasCommand $command)
    {
        $limit = $command->getLimit();
        
        return $this->LigaRepository->findLastLimit($limit);
    }
}