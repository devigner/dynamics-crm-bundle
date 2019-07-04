<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\Command;

use Devigner\DynamicsCRMBundle\Dynamics\ClientTrait;
use Devigner\DynamicsCRMBundle\Entity\DynamicsInterface;
use Devigner\DynamicsCRMBundle\Traits\EntityManagerTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractDynamicsCommand extends Command
{
    use ClientTrait;
    use EntityManagerTrait;

    /**
     * @var DynamicsInterface
     */
    protected $className;

    protected function configure(): void
    {
        $this
            ->addArgument('entity', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entity = $input->getArgument('entity');
        $this->className = null;
        foreach ($this->dynamicsConnector->getEntities() as $entityConfig) {
            if (isset($entityClass['dynamicsEntityName']) && $entityConfig->isMappedTo($entity)) {
                $this->className = $entityConfig->getClassName();
            }
        }

        if (null === $this->className) {
            $output->writeln(sprintf('Mapping not found for %s', $entity));
            return;
        }
    }
}
