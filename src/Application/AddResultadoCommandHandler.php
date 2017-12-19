<?php

namespace Application;

use Ddd\Domain\DomainEventPublisher;
use Domain\Model\PersistenceException;
use Domain\Model\Resultado\InvalidResultException;
use Domain\Model\Resultado\NewResultWasCreatedEvent;
use Domain\Model\Resultado\Resultado;
use Domain\Model\Resultado\ResultadoRepository;
use Domain\Model\Resultado\Set;

/**
 *
 * @author dvaqueiro
 */
class AddResultadoCommandHandler
{
    private $resultadoRepository;
    
    function __construct(ResultadoRepository $resultadoRepository)
    {
        $this->resultadoRepository = $resultadoRepository;
    }

    public function handle(AddResultadoCommad $command)
    {
        $data = $command->getData();

        $resultado = new Resultado(null, $data['idDivision'],
            $data['jugadorLocal'], $data['jugadorVisitante']);

        foreach ($data['sets'] as $value) {
            $resultado->addSet(new Set($value['juegosLocal'], $value['juegosVisitante']));
        }
        
        if(!$resultado->isValidResult()) {
            throw new InvalidResultException('El resultado no es correcto');
        }

        $resultado = $this->resultadoRepository->add($resultado);

        if(!$resultado->getIdResultado()) {
            throw new PersistenceException('Se ha producido un error guardando el resultado');
        }

        DomainEventPublisher::instance()->publish(
            new NewResultWasCreatedEvent($resultado->getIdResultado())
        );

        return $resultado;
    }
}