<?php

namespace Application;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class RegisterJugadorCommandHandler
{
    private $jugadorRepository;

    function __construct(\Domain\Model\JugadorRepository $jugadorRepository)
    {
        $this->jugadorRepository = $jugadorRepository;
    }

    public function handle(RegisterJugadorCommand $command)
    {
        $newJugador = $command->getJugador();
        $newJugador = $this->jugadorRepository->add($newJugador);
        if($newJugador->getId()) {
            $out = "Enhorabuena {$newJugador->getNombre()}, tu inscripción ha sido procesada "
            . "correctamente. Los organizadores gestionarán tu alta lo antes posible.";
        }
        //TODO: enviar email a persona encargada de formalizar registro

        return $out;
    }
}