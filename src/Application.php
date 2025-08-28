<?php

namespace App;

use App\Security\User;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @phpstan-type Settings array{
 *     APP_SERVER_TYPE?:string|null,
 *     APP_ROLE_PREFIX?:string|null,
 *     APP_URL?:string|null
 * }
 */
class Application
{
    public const string FORMAT_SHORT_DATE = 'd.m.Y';
    public const string FORMAT_SHORT_DATETIME = 'd.m.Y - H:i';
    public const string FORMAT_SHORT_TIME = 'H:i:s';

    private Security $security;
    private Kernel $kernel;
    /** @var Settings */
    private array $settings;

    /**
     * @param Settings $settings
     */
    public function __construct(Security $security, Kernel $kernel, array $settings)
    {
        $this->security = $security;
        $this->kernel = $kernel;
        $this->settings = $settings;
    }

    /**
     * Return type of server  (it's not symfony environment, don't confuse it).
     *
     * return values: 'prod', 'rec','dev' ou 'local'
     */
    public function getServerType(): string
    {
        /** @var array{APP_SERVER_TYPE?:string|null} $_ENV */
        $serverType = $this->settings['APP_SERVER_TYPE'] ?? 'prod';

        return mb_strtolower($serverType);
    }

    public function getGinaRolePrefix(): string
    {
        return $this->settings['APP_ROLE_PREFIX'] ?? '';
    }

    public function getUrl(): string
    {
        return $this->settings['APP_URL'] ?? '';
    }

    /**
     * Indication if the application run on a local PC.
     */
    public function isServerLocal(): bool
    {
        return 'local' === $this->getServerType();
    }

    /**
     * Return the symfony current environment.
     *
     * return values: 'prod', 'dev'
     */
    public function getEnvironment(): string
    {
        return $this->kernel->getEnvironment();
    }

    /**
     * Return indication of  the symfony debug mode.
     */
    public function isDebug(): bool
    {
        return $this->kernel->isDebug();
    }

    /**
     * @throws ExceptionApplication
     */
    public function getVersion(): string
    {
        $propertiesFile = __DIR__ . '/../release.properties';
        $propertiesContent = parse_ini_file($propertiesFile);
        if (!isset($propertiesContent['version'])) {
            throw new ExceptionApplication("'version' manquante dans le fichier release.properties");
        }

        return is_string($propertiesContent['version']) ? $propertiesContent['version'] : '';
    }

    public function getUser(): ?User
    {
        /** @var User */
        return $this->security->getToken()?->getUser();
    }
}
