<?php
namespace Geneve\HeaderBundle;

use Geneve\HeaderBundle\Service\BaseDemo;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class HeaderBundle extends AbstractBundle
{

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
      $services = $container->services();
      $services->set( BaseDemo::class);
      $builder->getDefinition( BaseDemo::class)
            ->setArgument('$param1', $config['param1'] )
            ->setArgument('$param2', $config['param2'] )
            ->setArgument('$param3', $config['param3'] )
            ;
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
            ->stringNode('param1')->defaultValue('default value1')->end()
            ->stringNode('param2')->defaultValue('default value2')->end()
            ->stringNode('param3')->defaultValue('default value3')->end()
            ->end()
        ;
    }
}
