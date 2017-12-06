<?php

namespace Infrastructure\Events;

use Ddd\Application\EventStore;
use Ddd\Domain\DomainEventPublisher;
use Domain\Events\CollectInMemoryDomainEventSuscriber;
use League\Tactician\Middleware;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class DomainEventsMiddelware implements Middleware
{
    private $eventStore;

    function __construct(EventStore $eventStore)
    {
        $this->eventStore = $eventStore;
    }

    public function execute($command, callable $next)
    {
        $domainEventPublisher = DomainEventPublisher::instance();
        $domainEventsCollector = new CollectInMemoryDomainEventSuscriber();
        $domainEventPublisher->subscribe($domainEventsCollector);

        $returnValue = $next($command);

        $events = $domainEventsCollector->events();
        foreach ($events as $event) {
            $this->eventStore->append($event);
        }

        return $returnValue;
    }
}