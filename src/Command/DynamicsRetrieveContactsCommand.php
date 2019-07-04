<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\Command;

use AlexaCRM\CRMToolkit\Entity;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DynamicsRetrieveContactsCommand extends AbstractDynamicsCommand
{
    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        parent::configure();
        $this
            ->setName('dynamics:retrieve-all-entities')
            ->addOption('limit', 'l', InputOption::VALUE_OPTIONAL, 'Default limit: 1', 10)
            ->addOption('page', 'p', InputOption::VALUE_OPTIONAL, 'Default page: 1', 1);
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $limit = $input->getOption('limit');
        $page = $input->getOption('page');
        $entity = $input->getArgument('entity');
        $config = $this->dynamicsConnector->getConfigByDynamicsEntity($entity);
        if (null === $config) {
            $output->writeln(sprintf('Entity mapping not found for %s', $entity));
            return;
        }

        $client = $this->dynamicsConnector->getService();
        $contacts = $client->retrieveMultipleEntities($config->getDynamicsEntityName(), $allPages = false, $pagingCookie = null, $limitCount = $limit, $pageNumber = $page, $simpleMode = false);
        foreach ($contacts->Entities as $contact) {
            /** @var Entity $contact */
            $output->writeln(sprintf('[%s] %s', $config->getDynamicsEntityName(), $contact->{$contact->getPrimaryIdField()}));
        }
    }
}
