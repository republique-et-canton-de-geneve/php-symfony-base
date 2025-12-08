<?php

namespace EtatGeneve\ResponseHeadersBundle\Tests\Unit;

use DG\BypassFinals;
use EtatGeneve\ResponseHeadersBundle\EventListener\ResponseListener;
use EtatGeneve\ResponseHeadersBundle\ResponseHeadersBundle;
use PHPStan\Symfony\Service;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\AbstractConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\AbstractServiceConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServiceConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\Routing\Loader\PhpFileLoader;



class ResponseHeadersBundleTest extends TestCase
{


protected function setUp(): void
   {
//        BypassFinals::enable();
    }


    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $id = 'response_headers.response_listener';
        //        $services = $container->services();
        //         $services->set($id, ResponseListener::class)
        //            ->arg('$headers', $config['headers'])
        //           ->tag('kernel.event_listener', ['event' => 'kernel.response', 'method' => 'onKernelResponse'])
        //       ; 
    }



    
    public function testLoadExtension(): void
    {
        $config = [];
         $builder = $this->getMockBuilder(ContainerBuilder::class)->disableOriginalConstructor()->getMock();

         $container = $this->getMockBuilder(ContainerConfigurator::class)->disableOriginalConstructor()->getMock();
         $services = $this->getMockBuilder(AbstractConfigurator::class)->disableOriginalConstructor()->getMock();
         $service = $this->createMock(AbstractServiceConfigurator::class);

        $container->method('services')->willReturn($services);
        //   $services->method('set')->willReturn($service);
        //   $service->method('atg')->willReturn($service);
        //   $service->method('tag')->willReturn($service);
        //   $responseHeaderBundle = new ResponseHeadersBundle();
        //    $this->loadExtension($config, $container, $builder);
        $this->assertTrue(true);
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
