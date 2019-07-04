<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use Devigner\DynamicsCRMBundle\Dynamics\ClientInterface;
use Devigner\DynamicsCRMBundle\Dynamics\EntityProxy;
use Devigner\DynamicsCRMBundle\Entity\DynamicsInterface;

interface MappingTransformerInterface
{
    /**
     * @param EntityManagerInterface $entityManager
     */
    public function setEntityManager(EntityManagerInterface $entityManager): void;

    /**
     * @param ClientInterface $dynamicsConnector
     */
    public function setDynamicsConnector(ClientInterface $dynamicsConnector): void;

    /**
     * @param EntityProxy $accountCRM
     * @param DynamicsInterface $localEntity
     * @return TransformResult
     */
    public function transformLocalWithRemote(EntityProxy $accountCRM, DynamicsInterface $localEntity): TransformResult;

    /**
     * @param EntityProxy $accountCRM
     * @param DynamicsInterface $localEntity
     * @return TransformResult
     */
    public function transformRemoteWithLocal(EntityProxy $accountCRM, DynamicsInterface $localEntity): TransformResult;
}
