<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DynamicsGetEntityFields extends AbstractDynamicsCommand
{
    public const NAME = 'dynamics:entity-fields';

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        parent::configure();
        $this
            ->setName(self::NAME)
            ->setDescription('Write a JSON object with detailed info about the requested entity')
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

        $entity = $this->dynamicsConnector->getService()->entity($config->getDynamicsEntityName());
        $result = [];
        foreach ($entity->attributes as $attribute) {
            $result[] = [
                'name' => $attribute->logicalName,
                'isValidForCreate' => $attribute->isValidForCreate,
                'isValidForUpdate' => $attribute->isValidForUpdate,
                'isPrimaryId' => $attribute->isPrimaryId,
                'isLookup' => $attribute->isLookup,
            ];
        }
        $file = '/app/src/Resources/doc/' . $config->getDynamicsEntityName() . '.json';
        file_put_contents($file, json_encode($result, JSON_PRETTY_PRINT));
        $output->writeln(sprintf('File written %s', $file));
    }
}
