<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\DataTransformer;

use AlexaCRM\CRMToolkit\Entity\EntityReference;
use Devigner\DynamicsCRMBundle\Dynamics\EntityProxy;
use Devigner\DynamicsCRMBundle\Dynamics\EntityReferenceProxy;
use Devigner\DynamicsCRMBundle\Entity\DynamicsInterface;
use Devigner\DynamicsCRMBundle\Event\DynamicsEvents;
use Devigner\DynamicsCRMBundle\Event\EntityCreateEvent;
use Devigner\DynamicsCRMBundle\Traits\EventDispatcherTrait;
use Devigner\DynamicsCRMBundle\Traits\PsrLoggerTrait;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ManyToOneTransformer extends SimpleTransform
{
    use EventDispatcherTrait;
    use PsrLoggerTrait;

    /**
     * @var string
     */
    protected $mappingClass;

    /**
     * @var string
     */
    protected $localMappingField;

    /**
     * @var EntityProxy
     */
    protected $proxy;

    /**
     * @param array $mapping
     */
    public function __construct(array $mapping)
    {
        $this->mappingClass = $mapping['mappingClass'];
        $this->localMappingField = $mapping['localMappingField'];

        parent::__construct($mapping);
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    protected function transformLocalWithRemoteValue($value = null)
    {
        /** @var EntityReferenceProxy $value */
        return $value->getValue('id');
    }

    /**
     * @param EntityProxy $accountCRM
     * @param DynamicsInterface $localEntity
     * @return TransformResult
     */
    public function transformLocalWithRemote(EntityProxy $accountCRM, DynamicsInterface $localEntity): TransformResult
    {
        $this->proxy = $accountCRM;
        if (!$accountCRM->hasValue($this->dynamicsVar)) {
            return new TransformResult($this->localVar, $this->dynamicsVar, null);
        }

        if (null === $remoteValue = $accountCRM->getValue($this->dynamicsVar)) {
            return new TransformResult($this->localVar, $this->dynamicsVar, null);
        }

        $value = $this->transformLocalWithRemoteValue($remoteValue);
        if (null === $value) {
            return new TransformResult($this->localVar, $this->dynamicsVar, $value);
        }

        $entity = $this->entityManager->getRepository($this->mappingClass)->findOneBy(['dynamicsId' => $value]);
        if (null === $entity) {
            $event = new EntityCreateEvent($this->mappingClass, $localEntity, $remoteValue);
            $this->eventDispatcher->dispatch(DynamicsEvents::ENTITY_CREATE_EVENT, $event);

            if (null === $entity = $event->getEntity()) {
                return new TransformResult($this->localVar, $this->dynamicsVar, null);
            }
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $propertyAccessor->setValue($localEntity, $this->localMappingField, $entity);

        return new TransformResult($this->localVar, $this->dynamicsVar, $entity);
    }

    /**
     * @param EntityProxy $accountCRM
     * @param DynamicsInterface $localEntity
     * @return TransformResult
     */
    public function transformRemoteWithLocal(EntityProxy $accountCRM, DynamicsInterface $localEntity): TransformResult
    {
        return new TransformResult($this->localVar, $this->dynamicsVar, null);

/*        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $value = $propertyAccessor->getValue($localEntity, $this->localMappingField);
        if (null === $value) {
            return new TransformResult($this->localVar, $this->dynamicsVar, null);
        }

        $config = $this->dynamicsConnector->getConfigByLocalEntityClass($this->mappingClass);
        if (null === $config) {
            return new TransformResult($this->localVar, $this->dynamicsVar, null);
        }*/

        //  $accountReference = $this->dynamicsConnector->getDynamicsEntityByKey($config->getDynamicsEntityName(), $this->dynamicsVar, $value->getDynamicsId());

//        $relation = $this->dynamicsConnector->getMetadata()->getEntityDefinition($config->getDynamicsEntityName())->oneToManyRelationships['contact_customer_accounts'];

        // $this->dynamicsConnector->getService()->associate($config->getDynamicsEntityName(), $accountReference->ID, new Relationship('contact_customer_accounts'), [$accountCRM]);

//        dump($relation);die();

/*        if (null === $accountReference) {
            return new TransformResult($this->localVar, $this->dynamicsVar, $value);
        }*/

        /*        if (!isset($accountCRM->{$this->dynamicsVar})) {
                    return new TransformResult($this->localVar, $this->dynamicsVar, $value);
                }*/

        //$accountCRM->{$this->dynamicsVar} = new Entity\EntityReference($config->getDynamicsEntityName(), $accountReference->ID);

       // return new TransformResult($this->localVar, $this->dynamicsVar, $value->getDynamicsId());

   }
}
