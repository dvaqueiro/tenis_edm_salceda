<?php

namespace Application\Player;

use Domain\Model\JugadorRepository;


/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class AllPlayersCommandHandler
{
    private $jugadorRepository;

    function __construct(JugadorRepository $jugadorRepository)
    {
        $this->jugadorRepository = $jugadorRepository;
    }

    public function handle(AllPlayersCommand $command)
    {
        $roles = $command->getRoles();
        
        return $this->jugadorRepository->findAllWithRoles($roles);
    }
}