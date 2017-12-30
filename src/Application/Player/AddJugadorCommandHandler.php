<?php

namespace Application\Player;

use Domain\Model\JugadorRepository;
use Infrastructure\Services\FileUploader;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class AddJugadorCommandHandler
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

    public function handle(AddJugadorCommand $command)
    {
        $newJugador = $command->getJugador();

        if($fotoFile = $newJugador->getFotoFile()) {
            $newFileName = $this->fileUploader->upload($fotoFile);
            $newJugador->setFoto($newFileName);
        }

        $newJugador = $this->jugadorRepository->add($newJugador);
        if($newJugador->getId()) {
            $out = "Se ha añadido el jugador correctamente";
        } else {
            $out = "Se ha producido un error añadiendo el nuevo juegador";
        }


        return $out;
    }
}