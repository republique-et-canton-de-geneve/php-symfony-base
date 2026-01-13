<?php

namespace EtatGeneve\ConfParameterBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use EtatGeneve\ConfParameterBundle\Controller\ConfParameterController;
use EtatGeneve\ConfParameterBundle\Entity\ConfParameterEntity;
use EtatGeneve\ConfParameterBundle\Service\ConfParameterManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use \symfony\component\dependencyinjection\loader\configurator\service;

class ConfParameterBundle extends AbstractBundle
{


    public function build(ContainerBuilder $container)
    {
        parent::build($container);


        $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver(
            [__DIR__ . '/../config/doctrine/mapping' => 'EtatGeneve\ConfParameterBundle\Entity'],
        ));
    }

    /**
     * @param array<string,array{condition:string}|array{}|array{string:string|array<string>}> $config
     **/
    public function loadExtension(array $config, ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void
    {
        $services = $containerConfigurator->services();
        $services->set(ConfParameterManager::class)
            ->arg('$EntityClassName', $config['entity_class'])
            ->arg('$managerRegistry', \symfony\component\dependencyinjection\loader\configurator\service('doctrine'))
            ->arg('$cache', \symfony\component\dependencyinjection\loader\configurator\service('cache.app'))
            ->set(ConfParameterController::class, ConfParameterController::class)
            ->public()
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
            ->scalarNode('entity_class')->defaultValue(ConfParameterEntity::class)->end()
            ->end()
        ;
    }


     public function getPath(): string
    {
        if (!isset($this->path)) {
            $this->path =dirname( __FILE__);
        }

        return $this->path;
    }
}
