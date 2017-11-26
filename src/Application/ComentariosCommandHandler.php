<?php


namespace Application;

use Domain\Model\ComentarioRepository;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class ComentariosCommandHandler
{
    /**
     * @var ComentarioRepository
     */
    private $comentarioRepository;

    function __construct(ComentarioRepository $comentarioRepository)
    {
        $this->comentarioRepository = $comentarioRepository;
    }
    
    public function handle(ComentariosCommand $command)
    {
        return $this->comentarioRepository->findAll($command->getLimit());
    }
}