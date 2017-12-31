<?php

namespace Application\Player;

use Domain\Model\JugadorRepository;
use Domain\Model\PersistenceException;
use Infrastructure\Services\FileUploader;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class DeleteJugadorCommandHandler
{
    /**
     * @var FileUploader
     */
    private $fileUploader;
    private $jugadorRepository;

    function __construct(JugadorRepository $jugadorRepository, FileUploader $fileUploader)
    {
        $this->jugadorRepository = $jugadorRepository;
        $this->fileUploader = $fileUploader;
    }

    public function handle(DeleteJugadorCommand $command)
    {
        $jugadorId = $command->getJugadorId();
        $jugador = $this->jugadorRepository->findById($jugadorId);

        if(!$jugador) {
            throw new PersistenceException("No se ha encontrado el jugador con identificador {$jugadorId}");
        }

        if($fotoFile = $jugador->getFotoFile()) {
            $ok = $this->fileUploader->deleteFile($fotoFile);
        }

        $ok = $this->jugadorRepository->delete($jugador->getId());
        
        if(!$ok) {
            throw new PersistenceException("No se ha podido eliminar al jugador con identificador {$jugadorId}");
        }
    }
}