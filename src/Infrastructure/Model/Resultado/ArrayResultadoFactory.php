<?php

namespace Infrastructure\Model\Resultado;

use Doctrine\Common\Collections\ArrayCollection;
use Domain\Model\Resultado\Resultado;
use Domain\Model\Resultado\ResultadoFactory;
use Domain\Model\Resultado\Set;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class ArrayResultadoFactory implements ResultadoFactory
{
    public function makeAll($data)
    {
        $resultados = new ArrayCollection();
        foreach ($data as $objData) {
            $resultado = $this->make($objData); 
            $resultados->set($resultado->getIdResultado(), $resultado);
        }

        return $resultados;
    }

    public function make($data)
    {
        $resultado = new Resultado(
            $data['id'], $data['division'], $data['idu1'], $data['idu2'],
            $data['nombre_local'], $data['nombre_visitante']
        );
        $resultado->addSet(new Set($data['j11'], $data['j12']));
        $resultado->addSet(new Set($data['j21'], $data['j22']));
        $resultado->addSet(new Set($data['j31'], $data['j32']));

        return $resultado;
    }
}
