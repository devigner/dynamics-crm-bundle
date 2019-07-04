<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\Dynamics;

use AlexaCRM\CRMToolkit\Entity\EntityReference as CRMToolkitEntityReference;
use AlexaCRM\Xrm\EntityReference as WebApiEntityReference;

final class EntityReferenceProxy
{
    /**
     * @var CRMToolkitEntityReference|WebApiEntityReference
     */
    private $entity;

    /**
     * @param CRMToolkitEntityReference|WebApiEntityReference $entity
     */
    public function __construct($entity)
    {
        $this->entity = $entity;
        /*
        $mode = $entity instanceof CRMToolkitEntityReference ? 'CRMToolkit' : 'WebApi';
        if (!in_array($mode, ['CRMToolkit', 'WebApi'], true)) {
            throw new WrongEntityException('Given entity is invalid');
        }
        */
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function hasValue(string $key): bool
    {
        if ($this->entity instanceof CRMToolkitEntityReference) {
            return isset($this->entity->{$key});
        }

        if ($this->entity instanceof WebApiEntityReference) {
            return isset($this->entity[$key]);
        }

        return false;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getValue(string $key)
    {
        if ($this->entity instanceof CRMToolkitEntityReference) {
            return $this->entity->{$key};
        }

        if ($this->entity instanceof WebApiEntityReference) {
            return $this->entity[$key];
        }

        return '';
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function setValue(string $key, $value): void
    {
        if ($this->entity instanceof CRMToolkitEntityReference) {
            $this->entity->{$key} = $value;
        }

        if ($this->entity instanceof WebApiEntityReference) {
            $this->entity[$key] = $value;
        }
    }

    /**
     * @return CRMToolkitEntityReference|WebApiEntityReference
     */
    public function getEntity()
    {
        return $this->entity;
    }
}
