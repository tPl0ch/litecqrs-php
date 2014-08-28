<?php

namespace LiteCQRS\Plugin\SymfonyBundle;

use LiteCQRS\Bus\AbstractEventMessageBus;
use LiteCQRS\Bus\EventName;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerEventBus extends AbstractEventMessageBus
{
    private $container;
    private $services;
    private $user;

    public function __construct(ContainerInterface $container, array $proxyFactories = array())
    {
        $this->container = $container;

        if ($this->container->has('security_context')) {
            /** @var \Symfony\Component\Security\Core\SecurityContext $context */
            $context = $this->container->get('security_context');
            $token = $context->getToken();

            if ($token) {
                $this->user = $token->getUser();
            };
        }

        parent::__construct($proxyFactories);
    }

    /**
     * @param \LiteCQRS\DomainEvent $event
     */
    protected function handle($event)
    {
        if ($this->user) {
            $this->addUserToEvent($event);
        }

        parent::handle($event);
    }

    protected function getHandlers(EventName $eventName)
    {
        $eventName = strtolower($eventName);

        if (!isset($this->services[$eventName])) {
            return array();
        }

        $services = array();
        foreach ($this->services[$eventName] as $id) {
            $services[] = $this->container->get($id);
        }

        return $services;
    }

    public function registerServices($services)
    {
        $this->services = $services;
    }

    /**
     * @param \LiteCQRS\DomainEvent $event
     */
    protected function addUserToEvent($event)
    {
        /** @var \LiteCQRS\Bus\EventMessageHeader $header */
        $header = $event->getMessageHeader();
        $header->sessionId = (string) $this->user;
    }
}

