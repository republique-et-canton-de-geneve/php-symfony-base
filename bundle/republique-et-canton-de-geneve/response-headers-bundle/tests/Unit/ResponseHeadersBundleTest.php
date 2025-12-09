<?php

namespace EtatGeneve\ResponseHeadersBundle\Tests\Unit;

use EtatGeneve\ResponseHeadersBundle\ResponseHeadersBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\Config\Definition\Loader\DefinitionFileLoader;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

use function dirname;

class ResponseHeadersBundleTest extends TestCase
{
    private ResponseHeadersBundle $responseHeadersBundle;

    public function setUp(): void
    {
        $this->responseHeadersBundle = new ResponseHeadersBundle();
    }

    public function testLoadExtension(): void
    {
        $config = ['headers' => []];
        $containerBuilder = new ContainerBuilder();
        $phpFileLoader = new PhpFileLoader($containerBuilder, new FileLocator(dirname(__DIR__) . '/Resources/config'));
        $instanceOf = [];
        $containerConfigurator = new ContainerConfigurator($containerBuilder, $phpFileLoader, $instanceOf, 'xx', 'xx');
        $this->responseHeadersBundle->loadExtension($config, $containerConfigurator, $containerBuilder);
        $this->assertTrue(true);
    }

    public function testConfigure()
    {
        $treeBuilder = new TreeBuilder('response_header');
        $fileLocator = new FileLocator();
        $defintionLoader = new DefinitionFileLoader($treeBuilder, $fileLocator);
        $definition = new DefinitionConfigurator($treeBuilder, $defintionLoader, '', '');
        $this->responseHeadersBundle->configure($definition);
        $this->assertTrue(true);
        $node = $definition->rootNode()->getNode(true);

        $processor = new Processor();
        $processor->process($node, [
            0 => [
                'headers' => [
                    'x1' => ['value' => 'val1'],
                    'x2' => 'toto',
                    'x3' => ['a', 'b'],
                ],
            ],
        ]);
    }
}
