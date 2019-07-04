<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\DependencyInjection;

use Devigner\DynamicsCRMBundle\Dynamics\ClientCRMToolkit;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use function dirname;

class RaakRdamDynamicsCRMExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__) . '/Resources/config'));
        $loader->load('services.yaml');

        $definition = $container->getDefinition(ClientCRMToolkit::class);
        $definition->addArgument($config['hostname']);
        $definition->addArgument($config['username']);
        $definition->addArgument($config['password']);
        $definition->addArgument($config['authMode']);
        $definition->addArgument($config['entities']);
    }
}
