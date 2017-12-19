<?php

namespace Application;

/**
 *
 * @author dvaqueiro
 */
class AddResultadoCommad
{
    private $data;

    function __construct($data)
    {

        $this->data = $data;
    }

    function getData()
    {
        return $this->data;
    }

}