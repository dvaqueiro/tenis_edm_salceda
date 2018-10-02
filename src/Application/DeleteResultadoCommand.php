<?php

namespace Application;

/**
 *
 * @author dvaqueiro
 */
class DeleteResultadoCommand 
{
    private $resultadoId;

    /**
     * @param int $resultadoId
     */
    public function __construct($resultadoId)
    {
        $this->resultadoId = $resultadoId;
    }
        
    /**
     * Get resultadoId
     *
     * @return int
     */
    public function getResultadoId()
    {
        return $this->resultadoId;
    }
}
