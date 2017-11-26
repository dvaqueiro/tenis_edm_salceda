<?php

namespace Application;

use DateTime;
use Domain\Model\Comentario;
use Domain\Model\ComentarioRepository;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class AddComentarioCommandHandler
{
    /**
     * @var ComentarioRepository
     */
    private $comentarioRepository;

    function __construct(ComentarioRepository $comentarioRepository)
    {
        $this->comentarioRepository = $comentarioRepository;
    }

    public function handle(AddComentarioCommand $command)
    {
        $id = null;
        $fecha = new DateTime();
        $usuario = $command->getUsuario();
        $contenido = $command->getContenido();
        $comentario = new Comentario($id, $fecha->format('D M d, Y G:i:s'), $usuario, $contenido);

        $comentario = $this->comentarioRepository->add($comentario);

        return $comentario;
    }
}