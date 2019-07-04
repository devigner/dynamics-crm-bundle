<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\Event;

use Devigner\DynamicsCRMBundle\Dynamics\EntityReferenceProxy;
use Devigner\DynamicsCRMBundle\Entity\DynamicsInterface;
use Symfony\Component\EventDispatcher\Event;

class EntityCreateEvent extends Event
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var EntityReferenceProxy
     */
    private $properties;

    /**
     * @var DynamicsInterface
     */
    private $relation;

    /**
     * @var null
     */
    private $entity = null;

    /**
     * @param string $className
     * @param DynamicsInterface $relation
     * @param EntityReferenceProxy $properties
     */
    public function __construct(string $className, DynamicsInterface $relation, EntityReferenceProxy $properties)
    {
        $this->className = $className;
        $this->relation = $relation;
        $this->properties = $properties;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @return DynamicsInterface
     */
    public function getRelation(): DynamicsInterface
    {
        return $this->relation;
    }

    /**
     * @return EntityReferenceProxy
     */
    public function getProperties(): EntityReferenceProxy
    {
        return $this->properties;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param mixed $entity
     */
    public function setEntity($entity): void
    {
        $this->entity = $entity;
    }
}
