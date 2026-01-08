<?php

use EtatGeneve\ConfParameterBundle\Controller\ConfParameterController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    $routes->add('conf_parameter_index', '/admin/conf_parameter')
        ->controller([ConfParameterController::class, 'show'])
        ->methods(['GET'])
    ;
};
