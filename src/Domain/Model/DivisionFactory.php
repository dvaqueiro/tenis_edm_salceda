<?php

namespace Domain\Model;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
interface DivisionFactory
{

    /**
     *
     * @param mixed[] $data
     * @return Division
     */
    public function makeAll($data);
}