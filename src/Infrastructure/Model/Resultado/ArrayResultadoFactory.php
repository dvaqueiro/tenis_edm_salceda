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
            //$idResultado, $idDivision, $idJugadorLocal, $idJugadorVisitante, $nombreJugadorLocal , $nombreJugadorVisitante
            $resultado = new Resultado($objData['id'], $objData['division'], $objData['idu1'], $objData['idu2'],
                $objData['nombre_local'], $objData['nombre_visitante']);
            $resultado->addSet(new Set($objData['j11'], $objData['j12']));
            $resultado->addSet(new Set($objData['j21'], $objData['j22']));
            $resultado->addSet(new Set($objData['j31'], $objData['j32']));
            $resultados->set($resultado->getIdResultado(), $resultado);
        }

        return $resultados;
    }
}