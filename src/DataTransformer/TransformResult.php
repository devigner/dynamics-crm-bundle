<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\DataTransformer;

class TransformResult
{
    /**
     * @var string
     */
    private $localVar;

    /**
     * @var string
     */
    private $dynamicsVar;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @param string $localVar
     * @param string $dynamicsVar
     * @param mixed $value
     */
    public function __construct(string $localVar, string $dynamicsVar, $value)
    {
        $this->localVar = $localVar;
        $this->dynamicsVar = $dynamicsVar;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getLocalVar(): string
    {
        return $this->localVar;
    }

    /**
     * @return string
     */
    public function getDynamicsVar(): string
    {
        return $this->dynamicsVar;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
