<?php

namespace EtatGeneve\ResponseHeadersBundle;

use EtatGeneve\ResponseHeadersBundle\EventListener\ResponseListener;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class ResponseHeadersBundle extends AbstractBundle
{
    /**
     * @param array<string,array{condition:string}|array{}|array{string:string|array<string>}> $config
     **/
    public function loadExtension(array $config, ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void
    {
        $id = 'response_headers.response_listener';
        $services = $containerConfigurator->services();
        $services->set($id, ResponseListener::class)
            ->arg('$headers', $config['headers'])
            ->tag('kernel.event_listener', ['event' => 'kernel.response', 'method' => 'onKernelResponse'])
        ;
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        /**
         * @var ArrayNodeDefinition
         */
        $root = $definition->rootNode();
        $root
            ->children()
            ->arrayNode('headers')
            ->useAttributeAsKey('name')
            ->arrayPrototype()
            ->beforeNormalization()
            // short description fot a header -> name: value
            ->castToArray()
            ->end()
            ->beforeNormalization()
            // short description fot a header -> name: [ array values ]
            ->ifArray()
            ->then(function (array $v): array {
                if (array_keys($v) === range(0, count($v) - 1)) {
                    return ['value' => $v];
                }

                return $v;
            })
            ->end()
            ->children()
            ->arrayNode('value')
            ->beforeNormalization()
            ->castToArray()
            ->end()
            ->scalarPrototype()->end()
            ->end()
            ->scalarNode('condition')->end()
            ->scalarNode('replace')->end()
            ->scalarNode('format')->end()
            ->end()
            ->end()
            ->end()
            ->end()
        ;
    }
}
