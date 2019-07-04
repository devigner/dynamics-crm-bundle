<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="app_dynamics_sync_log")
 */
class DynamicsActionLog
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var int
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     */
    private $userId;

    /**
     * @var string
     * @ORM\Column(name="action", type="string", length=255, nullable=true)
     */
    private $action;

    /**
     * @var string
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    use TimestampableEntity;

    public static function createEntry(string $content, string $action, ?int $userId = null): self
    {
        $entity = new self();
        $entity->content = $content;
        $entity->action = $action;
        $entity->userId = $userId;
        $entity->createdAt = new \DateTime();
        $entity->updatedAt = new \DateTime();

        return $entity;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @return string|null
     */
    public function getAction(): ?string
    {
        return $this->action;
    }
}
