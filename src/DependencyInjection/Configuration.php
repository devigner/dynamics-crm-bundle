<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('raak_rdam_dynamics_crm');
        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('hostname')->isRequired()->end()
                ->scalarNode('username')->isRequired()->end()
                ->scalarNode('password')->isRequired()->end()
                ->scalarNode('authMode')->isRequired()->end()
                ->arrayNode('entities')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('dynamicsEntityName')->isRequired()->end()
                            ->arrayNode('keyMapping')
                                //->useAttributeAsKey('name')
                                //->prototype('array')
                                    ->children()
                                        ->scalarNode('localKey')->end()
                                        ->scalarNode('remoteKey')->end()
                                    ->end()
                                //->end()
                            ->end()
                            ->arrayNode('mapping')->defaultValue([])->prototype('array')
                            ->children()
                                ->scalarNode('localField')->isRequired()->end()
                                ->scalarNode('dynamicsField')->isRequired()->end()
                                ->scalarNode('type')->end()
                                ->scalarNode('mappingClass')->end()
                                ->scalarNode('localMappingField')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
