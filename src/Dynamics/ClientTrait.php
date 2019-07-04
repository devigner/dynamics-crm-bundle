<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\Dynamics;

trait ClientTrait
{
    /**
     * @var ClientInterface
     */
    protected $dynamicsConnector;

    /**
     * @required
     * @param ClientInterface $dynamicsConnector
     */
    public function setDynamicsConnector(ClientInterface $dynamicsConnector): void
    {
        $this->dynamicsConnector = $dynamicsConnector;
    }
}
