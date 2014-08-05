<?php

namespace LiteCQRS\Bus\IdentityMap;

use LiteCQRS\Bus\EventQueue;

/**
 * Returns all events from {@see EventProviderInterface} instances
 * that are saved in the identity map.
 */
class EventProviderQueue implements EventQueue
{
    private $identityMap;

    public function __construct(IdentityMapInterface $identityMap)
    {
        $this->identityMap = $identityMap;
    }

    public function dequeueAllEvents()
    {
        $dequeueEvents = array();

        foreach ($this->identityMap->all() as $aggregateRoot) {
            $id = $this->identityMap->getAggregateId($aggregateRoot);
            $type = $this->identityMap->getAggregateType($aggregateRoot);

            foreach ($aggregateRoot->dequeueAppliedEvents() as $event) {
                $header = $event->getMessageHeader();
                $header->aggregateType = $type;
                $header->aggregateId   = $id;
                $header->setAggregate(null);

                $dequeueEvents[] = $event;
            }
        }

        return $dequeueEvents;
    }
}
