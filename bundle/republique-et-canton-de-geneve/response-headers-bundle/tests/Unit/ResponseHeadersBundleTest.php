<?php

namespace EtatGeneve\ResponseHeadersBundle\Tests\Unit;

use EtatGeneve\ResponseHeadersBundle\ResponseHeadersBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class ResponseHeadersBundleTest extends TestCase
{

    private ResponseHeadersBundle $reponseHeaderBundle ;
    public function setUp(): void {
                $this->reponseHeaderBundle =  new ResponseHeadersBundle();

    }


    // public function loadExtension(array $config, ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void
    // {
    //     $id = 'response_headers.response_listener';
    //     $services = $containerConfigurator->services();
    //     $services->set($id, ResponseListener::class)
    //     ->arg('$headers', $config['headers'])
    //     ->tag('kernel.event_listener', ['event' => 'kernel.response', 'method' => 'onKernelResponse'])
    //     ;
    // }




    public function testLoadExtension(): void
    {
        $config = [];
        $containerBuilder = new ContainerBuilder();
        $phpFileLoader = new PhpFileLoader($containerBuilder, new FileLocator(\dirname(__DIR__) . '/Resources/config'));
        $instanceOf = [];
        $containerConfigurator = new ContainerConfigurator($containerBuilder, $phpFileLoader, $instanceOf, 'xx', 'xx');
        $this->reponseHeaderBundle->loadExtension($config, $containerConfigurator, $containerBuilder);
        $this->assertTrue(true);
    }



    public function testConfigure() {


        // $treeBuilder = new TreeBuilder();
        // $definition = new DefinitionConfigurator();
    }

    // public function configure(DefinitionConfigurator $definition): void
    // {
    //     $definition->rootNode()
    //         ->children()
    //         ->arrayNode('headers')
    //         ->useAttributeAsKey('name')
    //         ->arrayPrototype()
    //         ->beforeNormalization()
    //         ->ifString()
    //         ->then(function (string $v): array {
    //             return ['value' => $v];
    //         })
    //         ->end()
    //         ->beforeNormalization()
    //         ->ifArray()
    //         ->then(function (array $v): array {
    //             if (array_keys($v) === range(0, count($v) - 1)) {
    //                 return ['value' => $v];
    //             }

    //             return $v;
    //         })
    //         ->end()
    //         ->children()
    //         ->arrayNode('value')
    //         ->beforeNormalization()
    //         ->ifString()
    //         ->then(function (string $v): array {
    //             return ['value' => $v];
    //         })
    //         ->end()
    //         ->scalarPrototype()->end()
    //         ->end()
    //         ->scalarNode('condition')->end()
    //         ->end()
    //         ->end()
    //         ->end()
    //         ->end()
    //     ;
    // }
}
