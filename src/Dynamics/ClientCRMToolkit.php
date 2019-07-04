<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\Dynamics;

use AlexaCRM\CRMToolkit\Client as AlexaCRMClient;
use AlexaCRM\CRMToolkit\Entity;
use AlexaCRM\CRMToolkit\KeyAttributes;
use AlexaCRM\CRMToolkit\Settings;
use AlexaCRM\CRMToolkit\SoapFault;
use Devigner\DynamicsCRMBundle\DependencyInjection\EntityConfig;
use Devigner\DynamicsCRMBundle\Entity\DynamicsInterface;
use Devigner\DynamicsCRMBundle\Exception\WrongEntityException;

final class ClientCRMToolkit extends AbstractClient
{
    /**
     * @var Settings
     */
    private $settings;

    /**
     * @var AlexaCRMClient
     */
    private $service;

    /**
     * @var Entity\MetadataCollection
     */
    private $metadata;

    /**
     * @return AlexaCRMClient
     * @throws \Exception
     */
    public function getService(): AlexaCRMClient
    {
        if ($this->service instanceof AlexaCRMClient) {
            return $this->service;
        }

        $this->settings = new Settings($this->options);
        $this->service = new AlexaCRMClient($this->settings, $this->cache, $this->logger);
        $this->metadata = Entity\MetadataCollection::instance($this->service);
        return $this->service;
    }

    /**
     * @param string $entityName
     * @param string|null $key
     * @return EntityProxy
     * @throws WrongEntityException
     */
    public function getEntity(string $entityName, ?string $key): EntityProxy
    {
        return new EntityProxy($this->getService()->entity($entityName, $key));
    }

    /**
     * @return Entity\MetadataCollection
     */
    public function getMetadata(): Entity\MetadataCollection
    {
        return $this->metadata;
    }

    /**
     * @param EntityConfig $config
     * @param EntityProxy $accountCRM
     * @param DynamicsInterface $localEntity
     * @return bool
     */
    protected function updateEntity(EntityConfig $config, EntityProxy $accountCRM, DynamicsInterface $localEntity): bool
    {
        try {
            $content = $this->updateRemoteWithLocal($config, $accountCRM, $localEntity);
            $content[] = $accountCRM->getEntity()->update();

            $this->syncLog(
                $content,
                self::ACTION_PUSH,
                $localEntity->getId()
            );
        } catch (SoapFault $exception) {
            $this->syncLog(
                $exception->getMessage(),
                sprintf('exception on action: %s [%s:%s]', self::ACTION_PUSH, __CLASS__, __LINE__),
                $localEntity->getId()
            );
            $this->warning('User sync throws exception', [$localEntity, $exception]);
            return false;
        }

        $this->info('User updated', [$localEntity]);

        return true;
    }

    /**
     * @param EntityConfig $config
     * @param EntityProxy $accountCRM
     * @param DynamicsInterface $localEntity
     * @return bool
     */
    protected function createEntity(EntityConfig $config, EntityProxy $accountCRM, DynamicsInterface $localEntity): bool
    {
        try {
            $this->syncLog(
                $this->updateRemoteWithLocal($config, $accountCRM, $localEntity),
                self::ACTION_CREATE_REMOTE_WITH_LOCAL,
                $localEntity->getId()
            );

            $accountId = $accountCRM->getEntity()->create();
        } catch (SoapFault $exception) {
            $this->syncLog(
                $exception->getMessage(),
                sprintf('exception on action: %s [%s:%s]', self::ACTION_CREATE_REMOTE_WITH_LOCAL, __CLASS__, __LINE__),
                $localEntity->getId()
            );
            $this->warning('User sync throws exception', [$localEntity, $exception]);
            return false;
        }

        if (false === $accountId) {
            $this->warning('User not created @ Dynamics', [$accountId, $localEntity]);
            return false;
        }

        $this->info('User created', [$accountId, $localEntity]);

        $localEntity->setDynamicsId($accountId);

        return true;
    }


    /**
     * @param string $entityName
     * @param string $key
     * @param mixed $value
     *
     * @return Entity|null
     * @throws \Exception
     */
    public function getDynamicsEntityByKey(string $entityName, string $key, $value): ?Entity
    {
        $contactKey = new KeyAttributes();
        $contactKey->add($key, $value);
        $entity = $this->getService()->entity($entityName, $contactKey);
        if (!$entity->exists) {
            return null;
        }

        return $entity;
    }
}
