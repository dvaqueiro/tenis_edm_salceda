<?php

namespace Domain\Model;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
interface ComentarioRepository
{

    public function add(Comentario $comentario);

    public function findAll($limit);
}