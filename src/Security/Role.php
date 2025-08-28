<?php

namespace App\Security;

use ReflectionClass;

/**
 * Gina role definition for application.
 */
class Role
{
    public const ADMINISTRATEUR = 'ROLE_ADMIN';
    public const SITE_ADMIN = 'ROLE_SITE_ADMIN';
    public const UTILISATEUR = 'ROLE_UTILISATEUR';

    public const ALL = 'ROLE_USER';                // all user of frontend, backend and anonymous have this role

    /**
     * @return array<string,mixed>
     */
    public static function getRoles(): array
    {
        $oClass = new ReflectionClass(__CLASS__);

        return $oClass->getConstants();
    }
}
