<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\Command;

use Devigner\DynamicsCRMBundle\Entity\DynamicsInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DynamicsUpdateEntityCommand extends AbstractDynamicsCommand
{
    public const NAME = 'dynamics:update-entity';
    public const DIRECTION_PUSH = 'push';
    public const DIRECTION_PULL = 'pull';

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        parent::configure();
        $this
            ->setName(self::NAME)
            ->addOption('direction', 'd', InputOption::VALUE_REQUIRED, 'push | pull', 'upstream')
            ->addArgument('value', InputArgument::REQUIRED);
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $direction = $input->getOption('direction');
        $value = $input->getArgument('value');
        $entity = $input->getArgument('entity');
        $config = $this->dynamicsConnector->getConfigByDynamicsEntity($entity);
        if (null === $config) {
            $output->writeln(sprintf('Entity mapping not found for %s', $entity));
            return;
        }

        if (!in_array($direction, [self::DIRECTION_PUSH, self::DIRECTION_PULL], true)) {
            $output->writeln(sprintf('Sync direction can only be upstream or downstream, %s given', $direction));
            return;
        }

        /** @var DynamicsInterface $user */
        $user = $this->entityManager->getRepository($config->getClassName())->findOneBy([$config->getKeyMapping()->getLocalKey() => $value]);
        if (null === $user) {
            $output->writeln(sprintf('Entity %s with value %s doesn\'t exists', $config->getClassName(), $config->getKeyMapping()->getLocalKey()));
            return;
        }

        if (self::DIRECTION_PUSH === $input->getOption('direction')) {
            $output->writeln('upstream sync');
            $this->dynamicsConnector->pushEntity($user);
            return;
        }

        if (self::DIRECTION_PULL === $input->getOption('direction')) {
            $output->writeln(sprintf('Pull data from Dynamics: %s', $config->getClassName()));
            $this->dynamicsConnector->pullEntity($user);
            return;
        }
    }
}
