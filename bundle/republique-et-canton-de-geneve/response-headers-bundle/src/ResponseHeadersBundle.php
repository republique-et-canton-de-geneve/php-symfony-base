<?php

namespace EtatGeneve\ResponseHeadersBundle;

use EtatGeneve\ResponseHeadersBundle\EventListener\ResponseListener;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class ResponseHeadersBundle extends AbstractBundle
{
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
        $definition->rootNode()
            ->children()
            ->arrayNode('headers')
            ->useAttributeAsKey('name')
            ->arrayPrototype()
            ->beforeNormalization()
            ->ifString()
            ->then(function (string $v): array {
                return ['value' => $v];
            })
            ->end()
            ->beforeNormalization()
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
            ->ifString()
            ->then(function (string $v): array {
                return ['value' => $v];
            })
            ->end()
            ->scalarPrototype()->end()
            ->end()
            ->scalarNode('condition')->end()
            ->end()
            ->end()
            ->end()
            ->end()
        ;
    }
}
