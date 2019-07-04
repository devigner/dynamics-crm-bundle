<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\Traits;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

trait PsrLoggerTrait
{
    use LoggerTrait;

    /**
     * The logger instance.
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Sets a logger.
     *
     * @required
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @param string $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = []): void
    {
        $this->logger->log($level, $message, $context);
    }
}
