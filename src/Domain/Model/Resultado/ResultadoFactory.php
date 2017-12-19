<?php

namespace Domain\Model\Resultado;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
interface ResultadoFactory
{
    function makeAll($data);
}