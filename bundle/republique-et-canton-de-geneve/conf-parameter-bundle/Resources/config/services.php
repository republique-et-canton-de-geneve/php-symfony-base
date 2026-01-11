<?php


namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Doctrine\ORM\EntityManagerInterface;
use Nbgrp\OneloginSamlBundle\Controller;
use Nbgrp\OneloginSamlBundle\EventListener;
use Nbgrp\OneloginSamlBundle\Idp;
use Nbgrp\OneloginSamlBundle\Onelogin;
use Nbgrp\OneloginSamlBundle\Security;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\HttpUtils;

return static function (ContainerConfigurator $container): void {
    $src = \dirname(__DIR__, 2);
    $container->services()
        ->defaults()
            ->autoconfigure()

        ->load('EtatGeneve\\ConfParameterBundle\\', $src.'/Controller/*')

        // ->set(Controller\Login::class)
        //     ->args([
        //         service('security.firewall.map'),
        //         param('nbgrp_onelogin_saml.authn_request'),
        //     ])

    ;
};
