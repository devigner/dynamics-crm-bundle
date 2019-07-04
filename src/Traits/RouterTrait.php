<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\Traits;

use Symfony\Component\Routing\RouterInterface;

trait RouterTrait
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @required
     * @param RouterInterface $router
     */
    public function setRouter(RouterInterface $router): void
    {
        $this->router = $router;
    }
}
