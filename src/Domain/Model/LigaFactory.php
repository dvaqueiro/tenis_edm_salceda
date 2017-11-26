<?php

namespace Domain\Model;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
interface LigaFactory
{

    function makeAll($data);

    function make($data);
}