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
}