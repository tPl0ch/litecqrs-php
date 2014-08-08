<?php

namespace LiteCQRS\Plugin\Doctrine;

use LiteCQRS\Bus\IdentityMap\IdentityMapInterface;
use LiteCQRS\EventProviderInterface;
use Doctrine\ORM\EntityManager;

class DoctrineIdentityMap implements IdentityMapInterface
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function add(EventProviderInterface $object)
    {
        $this->entityManager->persist($object);
    }

    public function all()
    {
        $aggregateRoots = array();
        $uow            = $this->entityManager->getUnitOfWork();

        foreach ($uow->getIdentityMap() as $class => $entities) {
            foreach ($entities as $entity) {
                if (!($entity instanceof EventProviderInterface)) {
                    // clear untracked entities from EntityManager
                    $classMeta = $this->entityManager->getClassMetadata(get_class($entity));
                    $this->entityManager->clear($classMeta->getName());

                    break;
                }

                $aggregateRoots[] = $entity;
            }
        }

        return $aggregateRoots;
    }

    public function getAggregateId(EventProviderInterface $object)
    {
        $class = $this->entityManager->getClassMetadata(get_class($object));

        if ($class->isIdentifierComposite) {
            return $class->getIdentifierValues($object);
        }

        return $class->getSingleIdReflectionProperty()->getValue($object);
    }

    public function getAggregateType(EventProviderInterface $object)
    {
        $class = $this->entityManager->getClassMetadata(get_class($object));

        return $class->getName();
    }
}

