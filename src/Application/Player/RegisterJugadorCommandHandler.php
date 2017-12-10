<?php

namespace Application\Player;

use Ddd\Domain\DomainEventPublisher;
use Domain\Model\JugadorRepository;
use Infrastructure\Services\FileUploader;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class RegisterJugadorCommandHandler
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

    public function handle(RegisterJugadorCommand $command)
    {
        $newJugador = $command->getJugador();
        
        if($fotoFile = $newJugador->getFotoFile()) {
            $newFileName = $this->fileUploader->upload($fotoFile);
            $newJugador->setFoto($newFileName);
        }

        $newJugador = $this->jugadorRepository->add($newJugador);
        if($newJugador->getId()) {
            $out = "Enhorabuena {$newJugador->getNombre()}, tu inscripción ha sido procesada "
            . "correctamente. Los organizadores gestionarán tu alta lo antes posible.";

            DomainEventPublisher::instance()->publish(
                new PlayerWasRegisterEvent($newJugador->getId())
            );
        }
        

        return $out;
    }
}