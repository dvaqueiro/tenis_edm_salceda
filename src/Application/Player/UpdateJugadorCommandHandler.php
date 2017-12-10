<?php

namespace Application\Player;

use Domain\Model\JugadorRepository;
use Infrastructure\Services\FileUploader;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class UpdateJugadorCommandHandler
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

    public function handle(UpdateJugadorCommand $command)
    {
        $jugador = $command->getJugador();
        
        if($fotoFile = $jugador->getFotoFile()) {
            $ok = $this->fileUploader->deleteFile($jugador->getFoto());
            $newFileName = $this->fileUploader->upload($fotoFile);
            $jugador->setFoto($newFileName);
        }

        $ok = $this->jugadorRepository->update($jugador);
        if($ok) {
            $out = "Enhorabuena {$jugador->getNombre()}, tus datos han sido modificados satisfactoriamente.";
        } else {
            $out = "Disculpas {$jugador->getNombre()}, se ha producido un error al modificar los datos, vuelva a intentarlo o póngase en contacto con "
                . "los organizadores.";
        }

        return $out;
    }
}