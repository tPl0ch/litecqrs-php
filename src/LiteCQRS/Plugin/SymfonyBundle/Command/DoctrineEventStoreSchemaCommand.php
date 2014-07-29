<?php
/**
 * @author Thomas Ploch <thomas.ploch@meinfernbus.de>
 */
namespace LiteCQRS\Plugin\SymfonyBundle\Command;

use LiteCQRS\Plugin\Doctrine\EventStore\TableEventStoreSchema;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DoctrineEventStoreSchemaCommand
 */
class DoctrineEventStoreSchemaCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('lite-cqrs:eventstore:schema');
        $this->setDescription('Generates the event store table in the configured connection');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Doctrine\DBAL\Connection $connection */
        $connectionServiceName = $this->getContainer()->getParameter('litecqrs.doctrine.table_event_store.connection.service');
        $connection = $this->getContainer()->get($connectionServiceName);
        $tableName  = $this->getContainer()->getParameter('litecqrs.doctrine.table_event_store.table_name');

        $schema = new TableEventStoreSchema($tableName);
        $connection->getSchemaManager()->createTable($schema->getTableSchema());

        $output->writeln("<info>Created table '$tableName'</info>");
    }
}
