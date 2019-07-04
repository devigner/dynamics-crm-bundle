<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\Traits;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

trait EventDispatcherTrait
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @required
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }
}
