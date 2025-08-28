<?php

namespace App\Twig;

use App\Application;
use App\ExceptionApplication;
use App\Parameter;
use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class Extension extends AbstractExtension
{
    public Application $application;
    public Parameter $parameter;

    public function __construct(Application $application, Parameter $parameter)
    {
        $this->application = $application;
        $this->parameter = $parameter;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('serverType', [$this, 'serverType'], ['is_safe' => ['html']]),
            new TwigFunction('version', [$this, 'version'], ['is_safe' => ['html']]),
            new TwigFunction('getParameter', [$this, 'getParameter'], ['is_safe' => ['html']]),
            new TwigFunction(
                'isModeMaintenance',
                [$this, 'isModeMaintenance'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction('isPageInfo', [$this, 'isPageInfo'], ['is_safe' => ['html']]),
            new TwigFunction('getPageInfo', [$this, 'getPageInfo'], ['is_safe' => ['html']]),
            new TwigFunction('getPageInfoId', [$this, 'getPageInfoId'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            'shortDate' => new TwigFilter('shortDate', [$this, 'shortDate'], ['is_safe' => ['html']]),
            'shortDateTime' => new TwigFilter('shortDateTime', [$this, 'shortDateTime'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @throws ExceptionApplication
     */
    protected function formatDate(mixed $dateTime, string $format): ?string
    {
        if (!$dateTime) {
            return null;
        }
        if ($dateTime instanceof DateTime) {
            return $dateTime->format($format);
        } else {
            throw new ExceptionApplication('Not a datetime object');
        }
    }

    public function shortDate(?DateTime $dateTime): ?string
    {
        return $this->formatDate($dateTime, Application::FORMAT_SHORT_DATE);
    }

    public function shortDateTime(?DateTime $dateTime): ?string
    {
        return $this->formatDate($dateTime, Application::FORMAT_SHORT_DATETIME);
    }

    public function version(): string
    {
        return $this->application->getVersion();
    }

    public function serverType(): string
    {
        return $this->application->getServerType();
    }

    public function getParameter(string $parameter): mixed
    {
        return $this->parameter->{$parameter};
    }

    /**
     * Indique si le site BO est en mode de maintenance.
     */
    public function isModeMaintenance(): bool
    {
        return (bool) $this->parameter->modeMaintenance;
    }

    /**
     * Indique si un message doit être affiché.
     */
    public function isPageInfo(): bool
    {
        return '' !== $this->parameter->pageInfo;
    }

    /**
     * Contenu du message pour le BO à afficher.
     */
    public function getPageInfo(): string
    {
        return $this->parameter->pageInfo;
    }

    /**
     * Retourne l'ID du message pour le BO à afficher.
     */
    public function getPageInfoId(): string
    {
        return md5($this->parameter->pageInfo);
    }
}
