<?php

namespace Domain\Model;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
interface ReservaFactory
{
    public function make($data);

    public function makeAll($data);
}