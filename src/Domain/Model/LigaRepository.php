<?php

namespace Domain\Model;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
interface LigaRepository
{

    public function findLastLimit($limit);

    /**
     *
     * @param int $idLiga
     * @return Liga
     */
    function findByIdOrLast($idLiga);

    /**
     *
     * @param string $nombre
     * @return int id de la nueva liga
     */
    public function add($nombre);

    /**
     *
     * @param string $nombre
     * @return bool
     */
    public function existsByNombre($nombre);
}