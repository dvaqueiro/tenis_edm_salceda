<?php

namespace Domain\Model;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class ArrayDivisionFactory implements DivisionFactory
{
    public function makeAll($data)
    {
        foreach ($data as $objData) {
            $out [] = new Division($objData['id'], $objData['idliga'], $objData['nombre'], $objData['categoria']);
        }

        return $out;
    }
}