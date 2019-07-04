<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\Dynamics;

use AlexaCRM\CRMToolkit\Entity as CRMToolkitEntity;
use AlexaCRM\Xrm\Entity as WebApiEntity;
use AlexaCRM\Xrm\EntityReference;
use Devigner\DynamicsCRMBundle\Exception\WrongEntityException;

final class EntityProxy
{
    /**
     * @var CRMToolkitEntity|WebApiEntity
     */
    private $entity;

    /**
     * @param CRMToolkitEntity|WebApiEntity $entity
     * @throws WrongEntityException
     */
    public function __construct($entity)
    {
        $this->entity = $entity;
        $mode = $entity instanceof CRMToolkitEntity ? 'CRMToolkit' : 'WebApi';
        if (!in_array($mode, ['CRMToolkit', 'WebApi'], true)) {
            throw new WrongEntityException('Given entity is invalid');
        }
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function hasValue(string $key): bool
    {
        if ($this->entity instanceof CRMToolkitEntity) {
            return isset($this->entity->{$key});
        }

        if ($this->entity instanceof WebApiEntity) {
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
        if ($this->entity instanceof CRMToolkitEntity) {
            $value = $this->entity->{$key};
            if ($value instanceof CRMToolkitEntity\EntityReference) {
                return new EntityReferenceProxy($value);
            }

            return $value;
        }

        if ($this->entity instanceof WebApiEntity) {
            $value = $this->entity[$key];
            if ($value instanceof EntityReference) {
                return new EntityReferenceProxy($value);
            }

            return $value;
        }
        
        return '';
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function setValue(string $key, $value): void
    {
        if ($this->entity instanceof CRMToolkitEntity) {
            $this->entity->{$key} = $value;
        }

        if ($this->entity instanceof WebApiEntity) {
            $this->entity[$key] = $value;
        }
    }

    /**
     * @return CRMToolkitEntity|WebApiEntity
     */
    public function getEntity()
    {
        return $this->entity;
    }
}
