<?php

namespace Application;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class ComentariosCommand
{
    private $limit;

    function __construct($limit)
    {
        $this->limit = $limit;
    }

    function getLimit()
    {
        return $this->limit;
    }
}