<?php


use EtatGeneve\ConfParameterBundle\Controller\ConfParameterController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;


return function (RoutingConfigurator $routes): void {
    $routes->add('conf_parameter_index', '/')
        ->controller([ConfParameterController::class, 'index'])
        ->methods(['GET'])
    ;
};


