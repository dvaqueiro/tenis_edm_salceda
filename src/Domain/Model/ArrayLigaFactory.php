<?php

namespace Domain\Model;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class ArrayLigaFactory implements LigaFactory
{
    public function make($data)
    {
        return new Liga($data['id'], $data['nombre']);
    }
}