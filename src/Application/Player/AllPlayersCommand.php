<?php

namespace Application\Player;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class AllPlayersCommand
{
    private $roles;

    function __construct($roles = [])
    {
        $this->roles = $roles;
    }

    function getRoles()
    {
        return $this->roles;
    }

}