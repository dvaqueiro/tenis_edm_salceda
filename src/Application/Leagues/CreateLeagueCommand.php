<?php

namespace Application\Leagues;

/**
 * Crea una liga completa: la liga, sus grupos (divisiones) y los jugadores
 * de cada grupo.
 */
class CreateLeagueCommand
{
    private $nombre;
    private $grupos;

    /**
     * @param string $nombre
     * @param array $grupos [['nombre' => '1ª grupo A', 'categoria' => 1, 'jugadores' => [12, 34, ...]], ...]
     */
    function __construct($nombre, $grupos)
    {
        $this->nombre = $nombre;
        $this->grupos = $grupos;
    }

    function getNombre()
    {
        return $this->nombre;
    }

    function getGrupos()
    {
        return $this->grupos;
    }
}
