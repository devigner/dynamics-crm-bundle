<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\DependencyInjection;

use Devigner\DynamicsCRMBundle\DataTransformer\MappingTransformerInterface;
use Devigner\DynamicsCRMBundle\DataTransformer\SimpleTransform;
use Devigner\DynamicsCRMBundle\Entity\KeyMapping;
use Devigner\DynamicsCRMBundle\Traits\EventDispatcherTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EntityConfig
{
    /**
     * @var string
     */
    protected $className;

    /**
     * @var string
     */
    protected $dynamicsEntityName;

    /**
     * @var array
     */
    protected $keyMapping;

    /**
     * @var array|MappingTransformerInterface[]
     */
    protected $mapping;

    /**
     * @param string $className
     * @param array $entityConfig
     */
    public function __construct(string $className, array $entityConfig)
    {
        $this->className = $className;
        $this->dynamicsEntityName = $entityConfig['dynamicsEntityName'];
        $this->keyMapping = new KeyMapping($entityConfig['keyMapping']['localKey'], $entityConfig['keyMapping']['remoteKey']);

        $this->mapping = [];
        foreach ($entityConfig['mapping'] as $mapping) {
            $transformer = SimpleTransform::class;
            if (isset($mapping['type'])) {
                $transformer = $mapping['type'];
                unset($mapping['type']);
            }
            $this->mapping[] = new $transformer($mapping);
        }
    }

    /**
     * @param string $entityName
     * @return bool
     */
    public function isMappedTo(string $entityName) : bool
    {
        return $this->className === $entityName;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function getDynamicsEntityName(): string
    {
        return $this->dynamicsEntityName;
    }

    /**
     * @return KeyMapping
     */
    public function getKeyMapping(): KeyMapping
    {
        return $this->keyMapping;
    }

    /**
     * @return array|MappingTransformerInterface[]
     */
    public function getMapping(): array
    {
        return $this->mapping;
    }
}
