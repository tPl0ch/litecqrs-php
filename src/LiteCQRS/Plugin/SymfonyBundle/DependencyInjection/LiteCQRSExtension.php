<?php

namespace LiteCQRS\Plugin\SymfonyBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class LiteCQRSExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if (isset($config['orm']) && $config['orm']['enabled'] === true) {
            $loader->load('orm.xml');

            $container->setParameter('litecqrs.orm.manager', $config['orm']['manager']);

            $container->setAlias('litecqrs.identity_map', 'litecqrs.identity_map.orm');
            $container->setAlias('litecqrs.repository', 'litecqrs.repository.orm');
        }

        if ($config['jms_serializer']) {
            $loader->load('jms_serializer.xml');
        }

        if ($config['crud']) {
            $loader->load('crud.xml');
        }

        if ($config['monolog']) {
            $loader->load('monolog.xml');
        }

        if ($config['swift_mailer']) {
            $loader->load('swiftmailer.xml');
        }

        if (isset($config['dbal_event_store']) && $config['dbal_event_store']['enabled'] === true) {
            $loader->load('dbal_event_store.xml');

            $container->setParameter(
                'litecqrs.doctrine.table_event_store.connection',
                $config['dbal_event_store']['connection']
            );

            $container->setParameter(
                'litecqrs.doctrine.table_event_store.connection.service',
                $config['dbal_event_store']['service']
            );

            $container->setParameter(
                'litecqrs.doctrine.table_event_store.table_name',
                $config['dbal_event_store']['table_name']
            );

            $container->setAlias('litecqrs.event_store', 'litecqrs.doctrine.event_store');
        }

        if ($config['couchdb_odm']) {
            $loader->load('couchdb_odm.xml');
            $container->setAlias('litecqrs.identity_map', 'litecqrs.identity_map.couchdb');
            $container->setAlias('litecqrs.repository', 'litecqrs.repository.couchdb');
        }

        if ($config['couchdb_event_store']) {
            $loader->load('couchdb_event_store.xml');
            $container->setAlias('litecqrs.event_store', 'litecqrs.couchdb.event_store');
        }

        if (isset($config['mongodb_event_store'])) {
            $loader->load('mongodb_event_store.xml');
            $container->setAlias('litecqrs.event_store', 'litecqrs.mongodb.event_store');
        }
    }
}

