<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\Traits;

use FOS\UserBundle\Model\UserManagerInterface;

trait UserManagerTrait
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @required
     * @param UserManagerInterface $userManager
     */
    public function setUserManager(UserManagerInterface $userManager): void
    {
        $this->userManager = $userManager;
    }
}
