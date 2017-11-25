<?php

namespace Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class ArrayDivisionFactory implements DivisionFactory
{
    public function makeAll($data)
    {
        $divisiones = new ArrayCollection();
        foreach ($data as $objData) {
            $divisiones->set($objData['id'], new Division($objData['id'], $objData['idliga'], $objData['nombre'], $objData['categoria']));
        }

        return $divisiones;
    }
}