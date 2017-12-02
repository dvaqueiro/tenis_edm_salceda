<?php

namespace Domain\Model;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
interface JugadorFactory
{

    public function make($data);

    public function makeAll($data);
}