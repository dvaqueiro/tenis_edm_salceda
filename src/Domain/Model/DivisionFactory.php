<?php

namespace Domain\Model;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
interface DivisionFactory
{

    public function make($data);

    /**
     *
     * @param mixed[] $data
     * @return Division
     */
    public function makeAll($data);
}