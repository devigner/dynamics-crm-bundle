<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DynamicsGetEntityFieldsRaw extends AbstractDynamicsCommand
{
    public const NAME = 'dynamics:entity-fields-raw';

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        parent::configure();
        $this
            ->setName(self::NAME)
            ->setDescription('Show a JSON object with detailed info about the requested entity without writing')
        ;
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->dynamicsConnector->getService();
        $entity = $this->dynamicsConnector->getService()->entity($input->getArgument('entity'));
        if (null === $entity) {
            return;
        }

        foreach ($entity->attributes as $attribute) {
            $output->writeln(json_encode([
                'name' => $attribute->logicalName,
                'isValidForCreate' => $attribute->isValidForCreate,
                'isValidForUpdate' => $attribute->isValidForUpdate,
                'isPrimaryId' => $attribute->isPrimaryId,
                'isLookup' => $attribute->isLookup,
            ]));
        }
    }
}
