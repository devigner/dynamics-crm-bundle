<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\Dynamics;

use AlexaCRM\Xrm\Entity;
use Psr\Log\LoggerAwareInterface;
use Devigner\DynamicsCRMBundle\DependencyInjection\EntityConfig;
use Devigner\DynamicsCRMBundle\Entity\DynamicsActionLog;
use Devigner\DynamicsCRMBundle\Entity\DynamicsInterface;
use Devigner\DynamicsCRMBundle\Traits\EntityManagerTrait;
use Devigner\DynamicsCRMBundle\Traits\EventDispatcherTrait;
use Devigner\DynamicsCRMBundle\Traits\PsrLoggerTrait;
use Symfony\Component\PropertyAccess\PropertyAccess;

abstract class AbstractClient implements ClientInterface, LoggerAwareInterface
{
    use PsrLoggerTrait;
    use EntityManagerTrait;
    use EventDispatcherTrait;

    public const ACTION_PULL = 'PULL';
    public const ACTION_PUSH = 'PUSH';
    public const ACTION_CREATE_LOCAL_WITH_REMOTE = 'CREATE_LOCAL_WITH_REMOTE';
    public const ACTION_CREATE_REMOTE_WITH_LOCAL = 'CREATE_REMOTE_WITH_LOCAL';

    /**
     * @var array|EntityConfig[]
     */
    protected $entities;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var mixed
     */
    protected $cache;

    /**
     * @var bool
     */
    protected $isMapped = false;

    public function __construct(string $serverUrl, string $username, string $password, string $authMode, array $entities, $cache = null)
    {
        $this->options = [
            'serverUrl' => $serverUrl,
            'username' => $username,
            'password' => $password,
            'authMode' => $authMode,
        ];

        $this->entities = [];
        foreach ($entities as $className => $entityConfig) {
            $this->entities[] = new EntityConfig($className, $entityConfig);
        }

        $this->cache = $cache;
    }

    /**
     * @return array|EntityConfig[]
     */
    public function getEntities(): array
    {
        if ($this->isMapped) {
            return $this->entities;
        }

        foreach ($this->entities as $entity) {
            foreach ($entity->getMapping() as $mappingTransformer) {
                if (method_exists($mappingTransformer, 'setEventDispatcher')) {
                    $mappingTransformer->setEventDispatcher($this->eventDispatcher);
                }
            }
        }

        $this->isMapped = true;

        return $this->entities;
    }

    /**
     * @param EntityConfig $config
     * @param EntityProxy $accountCRM
     * @param DynamicsInterface $localEntity
     * @return array
     */
    protected function updateLocalWithRemote(EntityConfig $config, EntityProxy $accountCRM, DynamicsInterface $localEntity): array
    {
        $content = [];
        $localEntity->setDynamicsId($accountCRM->getValue('id'));
        $localEntity->setLastDynamicsSync();
        foreach ($config->getMapping() as $mappingTransformer) {
            $mappingTransformer->setEntityManager($this->entityManager);
            $mappingTransformer->setDynamicsConnector($this);
            $result = $mappingTransformer->transformLocalWithRemote($accountCRM, $localEntity);
            $content[] = [
                'direction' => self::ACTION_PULL,
                'local' => $result->getLocalVar(),
                'remote' => $result->getDynamicsVar(),
                'value' => $result->getValue()
            ];
        }
        $this->entityManager->persist($localEntity);
        $this->entityManager->flush();

        return $content;
    }

    /**
     * @param EntityConfig $config
     * @param EntityProxy $accountCRM
     * @param DynamicsInterface $localEntity
     * @return array
     */
    protected function updateRemoteWithLocal(EntityConfig $config, EntityProxy $accountCRM, DynamicsInterface $localEntity): array
    {
        $content = [];
        foreach ($config->getMapping() as $mappingTransformer) {
            $mappingTransformer->setEntityManager($this->entityManager);
            $mappingTransformer->setDynamicsConnector($this);
            $result = $mappingTransformer->transformRemoteWithLocal($accountCRM, $localEntity);
            $content[] = [
                'direction' => self::ACTION_PUSH,
                'local' => $result->getLocalVar(),
                'remote' => $result->getDynamicsVar(),
                'value' => $result->getValue()
            ];
        }

        return $content;
    }

    abstract public function getEntity(string $entityName, ?string $key): EntityProxy;

    /**
     * @param DynamicsInterface $localEntity
     * @return bool
     * @throws \Exception
     */
    public function pullEntity(DynamicsInterface $localEntity): bool
    {
        if ($localEntity->isBusy()) {
            $this->debug('User is syncing', [$localEntity]);
            return false;
        }

        $localEntity->setBusy(true);

        if (null === $localEntity->getDynamicsId()) {
            $this->debug('User has no Dynamics connection', [$localEntity]);
            return false;
        }

        $config = $this->getConfigByLocalEntity($localEntity);
        if (null === $config) {
            $this->debug('Entity has no mapping', [$localEntity]);
            return false;
        }

        $accountCRM = $this->getEntity($config->getDynamicsEntityName(), $localEntity->getDynamicsId());

        $this->syncLog(
            $this->updateLocalWithRemote($config, $accountCRM, $localEntity),
            self::ACTION_PULL,
            $localEntity->getId()
        );

        $localEntity->setBusy(false);

        return true;
    }

    /**
     * @param DynamicsInterface $localEntity
     * @return bool
     * @throws \Exception
     */
    public function pushEntity(DynamicsInterface $localEntity): bool
    {
        if (null === $localEntity->getDynamicsId()) {
            return false;
        }

        if ($localEntity->isBusy()) {
            $this->debug('User is syncing', [$localEntity]);
            return false;
        }

        $config = $this->getConfigByLocalEntity($localEntity);
        if (null === $config) {
            $this->debug('Entity has no mapping', [$localEntity]);
            return false;
        }

        $accountCRM = $this->getEntity($config->getDynamicsEntityName(), $localEntity->getDynamicsId());
        if (null === $accountCRM) {
            return false;
        }

        $localEntity->setBusy(true);
        $this->updateEntity($config, $accountCRM, $localEntity);
        $localEntity->setBusy(false);
        return true;
    }

    abstract protected function updateEntity(EntityConfig $config, EntityProxy $accountCRM, DynamicsInterface $localEntity): bool;
    abstract protected function createEntity(EntityConfig $config, EntityProxy $accountCRM, DynamicsInterface $localEntity): bool;

    /**
     * @param DynamicsInterface $localEntity
     * @throws \Exception
     */
    public function syncUserWithDynamicsOnChange(DynamicsInterface $localEntity): void
    {
        if ($localEntity->isBusy()) {
            $this->debug('Entity is syncing', [$localEntity]);
            return;
        }

        $this->info('Entity has Dynamics Interface', [$localEntity]);

        $accountCRM = null;

        $localEntity->setBusy(true);
        $config = $this->getConfigByLocalEntity($localEntity);
        if (null === $config) {
            $this->debug('Entity has no mapping', [$localEntity]);
            return;
        }

        if (null === $localEntity->getDynamicsId()) {
            $this->info('Try to fetch remote entity', [$localEntity]);
            $accountCRM = $this->createLocalEntityWithDynamicsEntity($config, $localEntity);
        }

        if (null === $accountCRM) {
            $this->info('Try to create remote entity', [$localEntity]);
            $accountCRM = $this->getEntity($config->getDynamicsEntityName(), $localEntity->getDynamicsId());
        }

        if (null === $localEntity->getDynamicsId()) {
            $this->createEntity($config, $accountCRM, $localEntity);
            return;
        }

        $this->updateEntity($config, $accountCRM, $localEntity);

        $localEntity->setBusy(false);
    }

    /**
     * @param array|string $content
     * @param string $action
     * @param int $id
     */
    protected function syncLog($content, string $action, ?int $id = null): void
    {
        $value = $content;
        if (is_array($content)) {
            $value = json_encode($content);
        }
        $this->entityManager->persist(DynamicsActionLog::createEntry($value, $action, $id));
        $this->entityManager->flush();
    }

    /**
     * @param DynamicsInterface $localEntity
     * @return EntityConfig|null
     */
    public function getConfigByLocalEntity(DynamicsInterface $localEntity): ?EntityConfig
    {
        return $this->getConfigByLocalEntityClass(get_class($localEntity));
    }

    /**
     * @param string $entity
     * @return EntityConfig|null
     */
    public function getConfigByLocalEntityClass(string $entity): ?EntityConfig
    {
        foreach ($this->getEntities() as $entityConfig) {
            if ($entityConfig->isMappedTo($entity)) {
                return $entityConfig;
            }
        }

        return null;
    }

    /**
     * @param string $dynamicsEntity
     * @return EntityConfig|null
     */
    public function getConfigByDynamicsEntity(string $dynamicsEntity): ?EntityConfig
    {
        foreach ($this->getEntities() as $entityConfig) {
            if ($entityConfig->getDynamicsEntityName() === $dynamicsEntity) {
                return $entityConfig;
            }
        }

        return null;
    }

    /**
     * @param EntityConfig $config
     * @param DynamicsInterface $localEntity
     * @return EntityProxy|null
     * @throws \Exception
     */
    protected function createLocalEntityWithDynamicsEntity(EntityConfig $config, DynamicsInterface $localEntity): ?Entity
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $key = $propertyAccessor->getValue($localEntity, $config->getKeyMapping()->getLocalKey());
        if (null === $key) {
            return null;
        }

        $entity = $this->getDynamicsEntityByKey($config->getDynamicsEntityName(), $config->getKeyMapping()->getRemoteKey(), $key);
        if (null === $entity) {
            return null;
        }

        $this->syncLog(
            $this->updateLocalWithRemote($config, $entity, $localEntity),
            self::ACTION_CREATE_LOCAL_WITH_REMOTE,
            $localEntity->getId()
        );

        return $entity;
    }
}
