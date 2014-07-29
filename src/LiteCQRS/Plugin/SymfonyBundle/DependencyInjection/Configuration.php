<?php

namespace LiteCQRS\Plugin\SymfonyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $tb = new TreeBuilder();

        $tb
            ->root('lite_cqrs')
                ->children()
                    ->booleanNode('monolog')->defaultTrue()->end()
                    ->booleanNode('swift_mailer')->defaultFalse()->end()
                    ->booleanNode('orm')->defaultFalse()->end()
                    ->booleanNode('jms_serializer')->defaultFalse()->end()
                    ->arrayNode('dbal_event_store')
                        ->children()
                            ->booleanNode('enabled')->defaultFalse()->isRequired()->end()
                            ->scalarNode('connection')->defaultValue('default')->end()
                            ->scalarNode('service')->defaultValue('doctrine.dbal.default_connection')->end()
                            ->scalarNode('table_name')->defaultValue('litecqrs_events')->end()
                        ->end()
                    ->end()
                    ->booleanNode('couchdb_event_store')->defaultFalse()->end()
                    ->arrayNode('mongodb_event_store')
                        ->canBeUnset()
                        ->children()
                            ->scalarNode('database')->isRequired()->end()
                        ->end()
                    ->end()
                    ->booleanNode('couchdb_odm')->defaultFalse()->end()
                    ->booleanNode('crud')->defaultFalse()->end()
                ->end();

        return $tb;
    }
}
