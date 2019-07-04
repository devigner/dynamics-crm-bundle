<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

trait DynamicsEntityTrait
{
    /**
     * @var string
     *
     * @ORM\Column(name="dynamics_id", type="string", length=255, nullable=true, unique=true)
     */
    protected $dynamicsId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastDynamicsSync", type="datetime", nullable=true)
     */
    protected $lastDynamicsSync;

    /**
     * @var string
     *
     * @ORM\Column(name="syncToken", type="string", length=255, nullable=true)
     */
    protected $syncToken;

    /**
     * @var bool
     */
    protected $busy = false;

    /**
     * @return string|null
     */
    public function getDynamicsId(): ?string
    {
        return $this->dynamicsId;
    }

    /**
     * @param string $dynamicsId
     */
    public function setDynamicsId(string $dynamicsId): void
    {
        $this->dynamicsId = $dynamicsId;
    }

    public function setLastDynamicsSync(): void
    {
        $this->lastDynamicsSync = new \DateTime();
    }

    /**
     * @return string
     */
    public function getLastDynamicsSync(): string
    {
        if (null === $this->lastDynamicsSync) {
            return 'never';
        }

        return $this->lastDynamicsSync->format('Y-m-d H:i');
    }

    /**
     * @return string
     */
    public function getSyncToken(): ?string
    {
        return $this->syncToken;
    }

    /**
     * @param string $syncToken
     */
    public function setSyncToken(?string $syncToken): void
    {
        $this->syncToken = $syncToken;
    }

    /**
     * @return bool
     */
    public function isBusy(): bool
    {
        return $this->busy;
    }

    /**
     * @param bool $busy
     */
    public function setBusy(bool $busy): void
    {
        $this->busy = $busy;
    }
}
