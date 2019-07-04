<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\Dynamics;

use AlexaCRM\WebAPI\Client;
use AlexaCRM\WebAPI\ClientFactory;
use AlexaCRM\WebAPI\OData\AuthenticationException;
use AlexaCRM\WebAPI\OrganizationException;
use AlexaCRM\WebAPI\ToolkitException;
use AlexaCRM\Xrm\ColumnSet;
use Devigner\DynamicsCRMBundle\DependencyInjection\EntityConfig;
use Devigner\DynamicsCRMBundle\Entity\DynamicsInterface;
use Devigner\DynamicsCRMBundle\Exception\WrongEntityException;

final class ClientWebApi extends AbstractClient
{
    /**
     * @var Client
     */
    private $service;

    /**
     * @return Client
     * @throws \Exception
     */
    private function getService()
    {
        if ($this->service instanceof Client) {
            return $this->service;
        }

        $this->service = ClientFactory::createOnlineClient(
            $this->options['serverUrl'],
            '00000000-0000-0000-0000-000000000000',
            'Application Secret'
        );
        return $this->service;
    }

    /**
     * @param string $entityName
     * @param string|null $key
     * @return EntityProxy
     * @throws AuthenticationException
     * @throws OrganizationException
     * @throws ToolkitException
     * @throws WrongEntityException
     */
    public function getEntity(string $entityName, ?string $key): EntityProxy
    {
        return new EntityProxy($this->getService()->Retrieve($entityName, $key, new ColumnSet(true)));
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
            $this->service->Update($accountCRM->getEntity());

            $this->syncLog(
                $content,
                self::ACTION_PUSH,
                $localEntity->getId()
            );
        } catch (AuthenticationException $exception) {
            $this->warning('User sync throws exception', [$localEntity, $exception]);
            return false;
        } catch (OrganizationException $exception) {
            $this->warning('User sync throws exception', [$localEntity, $exception]);
            return false;
        } catch (ToolkitException $exception) {
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

            $accountId = $this->service->Create($accountCRM->getEntity());
        } catch (AuthenticationException $exception) {
            $this->warning('User sync throws exception', [$localEntity, $exception]);
            return false;
        } catch (OrganizationException $exception) {
            $this->warning('User sync throws exception', [$localEntity, $exception]);
            return false;
        } catch (ToolkitException $exception) {
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
     * @return EntityProxy|null
     * @throws \Exception
     */
    public function getDynamicsEntityByKey(string $entityName, string $key, $value): ?EntityProxy
    {
        return new EntityProxy($this->service->Retrieve($entityName, $key, new ColumnSet(true)));
    }

}
