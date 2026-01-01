<?php

namespace EtatGeneve\ConfParameterBundle;

use EtatGeneve\ConfParameterBundle\Service\ConfParameterManager;
use Symfony\Bridge\Doctrine\ManagerRegistry;

class ConfParameter
{

    public function __construct(
        private ConfParameterManager $confParameterManager
    ) {
        $confParameterManager->init($this);
    }
}
