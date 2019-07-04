<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\Traits;

use Doctrine\ORM\EntityManagerInterface;

trait EntityManagerTrait
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @required
     * @param EntityManagerInterface $entityManager
     */
    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }
}
