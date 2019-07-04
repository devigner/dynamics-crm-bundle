<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DynamicsConvertContactToUserCommand extends AbstractDynamicsCommand
{
    public const NAME = 'dynamics:convert-dynamics-contact-to-user';

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        parent::configure();
        $this
            ->setName(self::NAME)
            ->addArgument('emailAddress', InputArgument::REQUIRED)
        ;
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entity = $input->getArgument('entity');
        $config = $this->dynamicsConnector->getConfigByDynamicsEntity($entity);
        if (null === $config) {
            $output->writeln(sprintf('Entity mapping not found for %s', $entity));
            return;
        }

        $emailAddress = $input->getArgument('emailAddress');
        $user = $this->entityManager->getRepository($this->className)->findOneBy([$config->getKeyMapping()->getLocalKey() => $emailAddress]);
        if (null !== $user) {
            $output->writeln(sprintf('User %s already exists', $emailAddress));
            return;
        }

        $entity = $this->dynamicsConnector->getDynamicsEntityByKey($config->getDynamicsEntityName(), $config->getKeyMapping()->getRemoteKey(), $emailAddress);
        if (null === $entity) {
            $output->writeln(sprintf('Email address not found: %s', $emailAddress));
            return;
        }
    }
}
