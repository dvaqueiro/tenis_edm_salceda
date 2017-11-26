<?php

namespace Domain\Model;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
interface ComentarioFactory
{

    /**
     *
     * @param mixed[] $data
     * @return Comentario
     */
    public function makeAll($data);
}