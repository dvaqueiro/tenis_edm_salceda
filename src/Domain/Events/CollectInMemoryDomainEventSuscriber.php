<?php

namespace Domain\Events;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class CollectInMemoryDomainEventSuscriber implements \Ddd\Domain\DomainEventSubscriber
{
    private $events;

    function __construct()
    {
        $this->events = [];
    }

    public function handle($aDomainEvent)
    {
        $this->events[] = $aDomainEvent;
    }

    public function isSubscribedTo($aDomainEvent)
    {
        return true;
    }

    public function events()
    {
        return $this->events;
    }

    public function handleEvent(DomainEvent $event)
    {
        return $this->handle($event);
    }

}