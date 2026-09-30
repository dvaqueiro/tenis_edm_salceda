<?php

namespace Application\Leagues;

use Exception;

class InvalidLeagueException extends Exception
{
    private $errors;

    function __construct(array $errors)
    {
        parent::__construct(implode(' ', $errors));
        $this->errors = $errors;
    }

    function getErrors()
    {
        return $this->errors;
    }
}
