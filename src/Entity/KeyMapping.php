<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\Entity;

class KeyMapping
{
    /**
     * @var string
     */
    private $localKey;

    /**
     * @var string
     */
    private $remoteKey;

    /**
     * @param string $localKey
     * @param string $remoteKey
     */
    public function __construct(string $localKey, string $remoteKey)
    {
        $this->localKey = $localKey;
        $this->remoteKey = $remoteKey;
    }

    /**
     * @return string
     */
    public function getLocalKey(): string
    {
        return $this->localKey;
    }

    /**
     * @param string $localKey
     */
    public function setLocalKey(string $localKey): void
    {
        $this->localKey = $localKey;
    }

    /**
     * @return string
     */
    public function getRemoteKey(): string
    {
        return $this->remoteKey;
    }

    /**
     * @param string $remoteKey
     */
    public function setRemoteKey(string $remoteKey): void
    {
        $this->remoteKey = $remoteKey;
    }
}
