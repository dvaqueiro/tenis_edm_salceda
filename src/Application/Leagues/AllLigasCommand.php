<?php

namespace Application\Leagues;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class AllLigasCommand
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