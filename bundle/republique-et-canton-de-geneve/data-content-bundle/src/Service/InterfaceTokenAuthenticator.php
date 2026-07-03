<?php

declare(strict_types=1);

namespace EtatGeneve\DataContentBundle\Service;

interface InterfaceTokenAuthenticator
{
    public function getToken(): string;

    public function reset(): void;
}
