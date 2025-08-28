<?php

namespace App\Security;

use ReflectionClass;

/**
 * This class defines all actions
 * Depending on the user's role, these actions are allowed or forbidden (see ActionVoter class).
 */
class Action
{
    /**
     * Specific actions.
     */

    // display environment info in navbar
    public const string NAVBAR_ENVIRONNEMENT = 'navbar_environnement';
    public const string  HOMEPAGE = 'homepage';
    public const string  ADMIN_PAGE = 'admin_page';
    public const string  ADMIN_LOG = 'admin_log';
    public const string  ADMIN_PARAMETER = 'admin_parameter';
    public const string  ADMIN_PARAMETER_WRITE = 'admin_parameter_write';
    public const string  ADMIN_MAIL_TEST = 'admin_mail_test';

    /**
     * Returns the list of actions defined, in this class, by constants.
     *
     * @return array<string,mixed>
     */
    public static function getActions(): array
    {
        $reflectionClass = new ReflectionClass(self::class);

        return $reflectionClass->getConstants();
    }
}
