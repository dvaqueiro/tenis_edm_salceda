<?php

namespace Infrastructure\Events;

use Ddd\Application\EventStore;
use Doctrine\DBAL\Connection;
use JMS\Serializer\SerializerBuilder;
use ReflectionClass;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class DbalEventRepository implements EventStore
{
    private $dbal;

    function __construct(Connection $dbal)
    {
        $this->dbal = $dbal;
    }
    
    public function allStoredEventsSince($anEventId)
    {
        
    } 

    public function append($aDomainEvent)
    {
        $sql = "INSERT INTO domain_events (class, ocurredOn, payload) VALUES (?,?,?)";
        $reflect = new ReflectionClass($aDomainEvent);
        $stmt = $this->dbal->prepare($sql);
        $stmt->bindValue(1, $reflect->getShortName());
        $stmt->bindValue(2, $aDomainEvent->occurredOn());
        $stmt->bindValue(3, SerializerBuilder::create()->build()->serialize($aDomainEvent, 'json'));
        return $stmt->execute();
    }
}