<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\Traits;

use FOS\UserBundle\Util\TokenGeneratorInterface;

trait TokenGeneratorTrait
{
    /**
     * @var TokenGeneratorInterface
     */
    protected $tokenGenerator;

    /**
     * @required
     * @param TokenGeneratorInterface $tokenGenerator
     */
    public function setTokenGenerator(TokenGeneratorInterface $tokenGenerator): void
    {
        $this->tokenGenerator = $tokenGenerator;
    }
}
