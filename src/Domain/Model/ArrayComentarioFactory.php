<?php

namespace Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class ArrayComentarioFactory implements ComentarioFactory
{
    public function makeAll($data)
    {
        $comentarios = new ArrayCollection();
        foreach ($data as $objData) {
            $comentarios->set($objData['id'], new Comentario($objData['id'], $objData['fecha'], $objData['usuario'], $objData['comentario']));
        }

        return $comentarios;
    }
}