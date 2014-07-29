<?php
/**
 * @author Thomas Ploch <thomas.ploch@meinfernbus.de>
 */
namespace LiteCQRS\Plugin\SymfonyBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class DoctrineEventStorePass
 */
class DoctrineEventStorePass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('litecqrs.dbal.schema_listener')) {
            return;
        }

        $definition = $container->getDefinition('litecqrs.dbal.schema_listener');
        $tags = $definition->getTags();
        $tags['doctrine.event_listener'][0]['connection'] = $container->getParameter('litecqrs.doctrine.table_event_store.connection');
        $definition->setTags($tags);
        $container->setDefinition('litecqrs.dbal.schema_listener', $definition);

        $definition = $container->getDefinition('litecqrs.doctrine.event_store');
        $definition->replaceArgument(0, new Reference($container->getParameter('litecqrs.doctrine.table_event_store.connection.service')));
        $container->setDefinition('litecqrs.doctrine.event_store', $definition);
    }
}
