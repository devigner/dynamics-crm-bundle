<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\Entity;

use Devigner\DynamicsCRMBundle\DataTransformer\MappingTransformerInterface;

interface DynamicsInterface
{
    /**
     * FOSUser inheritance
     * @return int
     */
    public function getId();

    /**
     * Set last sync date
     */
    public function setLastDynamicsSync(): void;

    /**
     * @return string
     */
    public function getLastDynamicsSync(): string;

    /**
     * @return string|null
     */
    public function getDynamicsId(): ?string;

    /**
     * @param string $dynamicsId
     */
    public function setDynamicsId(string $dynamicsId): void;
    /**
     * @return bool
     */
    public function isBusy(): bool;

    /**
     * @param bool $busy
     */
    public function setBusy(bool $busy): void;

    /**
     * @return bool
     */
    public function canSync(): bool;
}
