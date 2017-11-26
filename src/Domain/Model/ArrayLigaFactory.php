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

    public function makeAll($data)
    {
        foreach ($data as $row) {
            $ligas[] = new Liga($row['id'], $row['nombre']);
        }

        return $ligas;
    }
}