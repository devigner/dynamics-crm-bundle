<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\DataTransformer;

use AlexaCRM\CRMToolkit\Entity;
use Devigner\DynamicsCRMBundle\Dynamics\ClientTrait;
use Devigner\DynamicsCRMBundle\Dynamics\EntityProxy;
use Devigner\DynamicsCRMBundle\Entity\DynamicsInterface;
use Devigner\DynamicsCRMBundle\Traits\EntityManagerTrait;
use Symfony\Component\PropertyAccess\PropertyAccess;

class SimpleTransform implements MappingTransformerInterface
{
    use EntityManagerTrait;
    use ClientTrait;

    /**
     * @var string
     */
    protected $localVar;

    /**
     * @var string
     */
    protected $dynamicsVar;

    /**
     * @param array $mapping
     */
    public function __construct(array $mapping)
    {
        $this->localVar = $mapping['localField'];
        $this->dynamicsVar = $mapping['dynamicsField'];
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    protected function transformRemoteWithLocalValue($value = null)
    {
        return $value;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    protected function transformLocalWithRemoteValue($value = null)
    {
        return $value;
    }

    /**
     * @param EntityProxy $accountCRM
     * @param DynamicsInterface $localEntity
     * @return TransformResult
     */
    public function transformLocalWithRemote(EntityProxy $accountCRM, DynamicsInterface $localEntity): TransformResult
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        if (!$accountCRM->hasValue($this->dynamicsVar)) {
            return new TransformResult($this->localVar, $this->dynamicsVar, null);
        }

        $value = $this->transformLocalWithRemoteValue($accountCRM->getValue($this->dynamicsVar));
        if (null === $value) {
            return new TransformResult($this->localVar, $this->dynamicsVar, $value);
        }

        $propertyAccessor->setValue($localEntity, $this->localVar, $value);

        return new TransformResult($this->localVar, $this->dynamicsVar, $value);
    }

    /**
     * @param EntityProxy $accountCRM
     * @param DynamicsInterface $localEntity
     * @return TransformResult
     */
    public function transformRemoteWithLocal(EntityProxy $accountCRM, DynamicsInterface $localEntity): TransformResult
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $value = $this->transformRemoteWithLocalValue($propertyAccessor->getValue($localEntity, $this->localVar));
        if (null === $value) {
            return new TransformResult($this->localVar, $this->dynamicsVar, $value);
        }

        $accountCRM->setValue($this->dynamicsVar, $value);

        return new TransformResult($this->localVar, $this->dynamicsVar, $value);
    }
}
