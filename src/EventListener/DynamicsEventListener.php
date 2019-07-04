<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Devigner\DynamicsCRMBundle\Dynamics\ClientTrait;
use Devigner\DynamicsCRMBundle\Entity\DynamicsInterface;

class DynamicsEventListener
{
    use ClientTrait;

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $localEntity = $args->getEntity();
        if (!$localEntity instanceof DynamicsInterface) {
            return;
        }

        if (!$localEntity->canSync()) {
            return;
        }

        $this->dynamicsConnector->syncUserWithDynamicsOnChange($localEntity);
    }
}
