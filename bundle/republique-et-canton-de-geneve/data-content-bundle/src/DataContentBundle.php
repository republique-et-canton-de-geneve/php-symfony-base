<?php

namespace EtatGeneve\DataContentBundle;

use EtatGeneve\DataContentBundle\Service\DataContent;
use EtatGeneve\DataContentBundle\Service\TokenAuthenticator;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * @phpstan-import-type DataContentConfig from DataContent
 */
class DataContentBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        /**
         * @var ArrayNodeDefinition
         */
        $rootNode = $definition->rootNode();
        /** @var ArrayNodeDefinition $rootNode */
        $child = $rootNode->children();
        $child->scalarNode('checkSSL')->defaultValue(1)->end();
        $child->scalarNode('applicationId')->isRequired()->cannotBeEmpty()->info('Application Name Id')->end();
        $child->scalarNode('clientId')->cannotBeEmpty()->info('Client Id for token authentification')->end();
        $child->scalarNode('clientSecret')->cannotBeEmpty()->info('Client secret for token authentification')->end();
        $child->scalarNode('restUrl')->isRequired()->cannotBeEmpty()->info('Rest Url for DataContent')->end();
        $child->scalarNode('baseId')->isRequired()->cannotBeEmpty()->info('Base Id for DataContent')->end();
        $child->scalarNode('timeout')->defaultValue(10)->info('Timout conection for DataContent')->end();

        $child->scalarNode('tokenAuthenticatorClass')->defaultValue(null)->info('Service for token authentification')->end();

        $child->scalarNode('username')->info('Username for token authentification')->end();
        $child->scalarNode('password')->info('Password secret for token authentification')->end();
        $child->scalarNode('audience')->info('Audience for token request')->end();
        $child->scalarNode('tokenTimeout')->defaultValue(10)->info('Timout conection for authentification')->end();
        $child->scalarNode('tokenAuthSsoUrl')->info('Timout connection for token authentification')->end();
    }

    /**
     * @param array<string,array{condition:string}|array{}|array{string:string|array<string>}> $config
     **/
    public function loadExtension(array $config, ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void
    {
        $tokenAuthenticatorClass = $config['tokenAuthenticatorClass'];
        $services = $containerConfigurator->services();
        $services
            ->defaults()
            ->autowire()      // Automatically injects dependencies in your services.
            ->autoconfigure();
        if (!$tokenAuthenticatorClass) {
            $services->set(TokenAuthenticator::class)
                ->arg('$config', $config);
        }
        $services->set(DataContent::class)
            ->arg('$config', $config);
    }
}
