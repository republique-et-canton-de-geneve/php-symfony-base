<?php

namespace EtatGeneve\DataContentBundle;

use EtatGeneve\DataContentBundle\Service\Datacontent;
use EtatGeneve\DataContentBundle\Service\TokenAuthenticator;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * @phpstan-type TokenAuthenticatorConfig array{
 * checkSSL: bool,
 * applicationId: string,
 * clientId: string,
 * clientSecret: string,
 * username : string,
 * password : string,
 * tokenTimeout : int,
 * timeout : int,
 * tokenAuthSsoUrl : string,
 * restUrl : string,
 * baseId : string,
 * audience : string
 * }
 */
class DataContentBundle extends AbstractBundle
{
    /**
     * @param array<string,array{condition:string}|array{}|array{string:string|array<string>}> $config
     **/
    public function loadExtension(array $config, ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void
    {
        $id = 'datacontent.token_authenticator';
        $services = $containerConfigurator->services();
        $services
        ->defaults()
            ->autowire()      // Automatically injects dependencies in your services.
            ->autoconfigure()
            ->set(TokenAuthenticator::class)
            ->arg('$config', $config)
            ->set(Datacontent::class)
            ->arg('$config', $config)
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
            ->scalarNode('applicationId')->isRequired()->cannotBeEmpty()->info('Application Name Id')->end()
            ->booleanNode('checkSSL')->defaultTrue()->end()
            ->scalarNode('clientId')->isRequired()->cannotBeEmpty()->info('Client Id for token authentification')->end()
            ->scalarNode('clientSecret')->isRequired()->cannotBeEmpty()->info('Client secret for token authentification')->end()
            ->scalarNode('username')->isRequired()->cannotBeEmpty()->info('Username for token authentification')->end()
            ->scalarNode('password')->isRequired()->cannotBeEmpty()->info('Password secret for token authentification')->end()
            ->scalarNode('tokenTimeout')->defaultValue(10)->info('Timout conection for authentification')->end()
            ->scalarNode('tokenAuthSsoUrl')->isRequired()->cannotBeEmpty()->info('Timout connection for token authentification')->end()
            ->scalarNode('restUrl')->isRequired()->cannotBeEmpty()->info('Rest Url for datacontent')->end()
            ->scalarNode('baseId')->isRequired()->cannotBeEmpty()->info('Base Id for datacontent')->end()
            ->scalarNode('audience')->isRequired()->cannotBeEmpty()->info('Audience for token request')->end()
            ->scalarNode('timeout')->defaultValue(10)->info('Timout conection for datacontent')->end()

        ;
    }
}
