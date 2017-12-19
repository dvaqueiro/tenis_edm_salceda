<?php

namespace Infrastructure\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Domain\Model\Division;
use Domain\Model\DivisionFactory;

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

    public function make($data)
    {
        return new Division($data['id'], $data['idliga'], $data['nombre'], $data['categoria']);
    }
}