<?php

namespace Application;

use Ddd\Domain\DomainEventPublisher;
use Domain\Model\PersistenceException;
use Domain\Model\Resultado\InvalidResultException;
use Domain\Model\Resultado\ResultWasDeleteEvent;
use Domain\Model\Resultado\Resultado;
use Domain\Model\Resultado\ResultadoRepository;
use Domain\Model\Resultado\Set;

/**
 *
 * @author dvaqueiro
 */
class DeleteResultadoCommandHandler
{
    private $resultadoRepository;
    
    function __construct(ResultadoRepository $resultadoRepository)
    {
        $this->resultadoRepository = $resultadoRepository;
    }

    public function handle(DeleteResultadoCommand $command)
    {
        $resultadoId = $command->getResultadoId();

        $resultado = $this->resultadoRepository->find($resultadoId);
        if($resultado->getIdResultado() == null) {
            throw new PersistenceException('El resultado no ha sido encontrado');
        }

        $this->resultadoRepository->remove($resultadoId);

        DomainEventPublisher::instance()->publish(
            new ResultWasDeleteEvent($resultado->getIdResultado())
        );

        return true;
    }
}
