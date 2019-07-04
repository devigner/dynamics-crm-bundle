<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\Dynamics;

use Devigner\DynamicsCRMBundle\DependencyInjection\EntityConfig;
use Devigner\DynamicsCRMBundle\Entity\DynamicsInterface;

interface ClientInterface
{
    /**
     * @param DynamicsInterface $localEntity
     */
    public function syncUserWithDynamicsOnChange(DynamicsInterface $localEntity): void;

    /**
     * @param DynamicsInterface $localEntity
     * @return bool
     */
    public function pullEntity(DynamicsInterface $localEntity): bool;

    /**
     * @param DynamicsInterface $localEntity
     * @return bool
     */
    public function pushEntity(DynamicsInterface $localEntity): bool;

    /**
     * @return array|EntityConfig[]
     */
    public function getEntities(): array;

    /**
     * @param string $entityName
     * @param string $key
     * @param mixed $value
     *
     * @return mixed
     */
    public function getDynamicsEntityByKey(string $entityName, string $key, $value);

    /**
     * @param string $dynamicsEntity
     * @return EntityConfig|null
     */
    public function getConfigByDynamicsEntity(string $dynamicsEntity): ?EntityConfig;

    /**
     * @param DynamicsInterface $localEntity
     * @return EntityConfig|null
     */
    public function getConfigByLocalEntity(DynamicsInterface $localEntity): ?EntityConfig;

    /**
     * @param string $entity
     * @return EntityConfig|null
     */
    public function getConfigByLocalEntityClass(string $entity): ?EntityConfig;
}
