<?php

namespace Instasent\ResqueBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('instasent_resque');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('vendor_dir')
                    ->defaultValue('%kernel.root_dir%/../vendor')
                    ->cannotBeEmpty()
                    ->info('Set the vendor dir')
                ->end()
                ->scalarNode('prefix')
                    ->defaultNull()
                    ->end()
                ->scalarNode('class')
                    ->defaultValue('Instasent\ResqueBundle\Resque')
                    ->cannotBeEmpty()
                    ->info('Set the resque class dir')
                ->end()
                ->arrayNode('auto_retry')
                    ->beforeNormalization()
                        ->ifArray()
                        ->then(function ($var) {
                            if (array_key_exists(0, $var)) {
                                return array($var);
                            }

                            return $var;
                        })
                    ->end()
                    ->prototype('array')
                        ->prototype('scalar')->end()
                    ->end()
                    ->info('Set auto retry strategy')
                ->end()
                ->arrayNode('redis')
                    ->info('Redis configuration')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('host')
                            ->defaultValue('localhost')
                            ->cannotBeEmpty()
                            ->info('The redis hostname')
                        ->end()
                        ->scalarNode('port')
                            ->defaultValue(6379)
                            ->cannotBeEmpty()
                            ->info('The redis port')
                        ->end()
                        ->scalarNode('database')
                            ->defaultValue(0)
                            ->info('The redis database')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('worker')
                    ->info('Worker Server configuration')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('root_dir')
                            ->defaultValue('%kernel.root_dir%')
                            ->cannotBeEmpty()
                            ->info('The root dir of worker registered app')
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('worker_class')
                    ->defaultValue('Resque_Worker')
                    ->cannotBeEmpty()
                    ->info('Set the resque class dir')
                ->end()
                ->scalarNode('worker_single_class')
                    ->defaultValue(WorkerSingle::class)
                    ->cannotBeEmpty()
                    ->info('Set the resque class dir')
                ->end()
                ->scalarNode('worker_scheduler_class')
                    ->defaultValue('ResqueScheduler_Worker')
                    ->cannotBeEmpty()
                    ->info('Set the resque class dir')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
