<?php

namespace App\Menu;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MenuTwig extends AbstractExtension
{
    /**
     * @var array<array<array<MenuItem>>>
     */
    public array $menu = [];
    public UrlGeneratorInterface $urlGenerator;
    public ?Request $request;
    public string $activeUri;

    public function __construct(RouterInterface $router, RequestStack $requestStack)
    {
        /** @var Router $r */
        $r = $router;
        $this->urlGenerator = $r->getGenerator();
        $this->request = $requestStack->getCurrentRequest();
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getMenuItems', [$this, 'getMenuItems']),
            new TwigFunction('getMenuSubItems', [$this, 'getMenuSubItems']),
            new TwigFunction('getMenuOptions', [$this, 'getMenuOptions']),
            new TwigFunction('isMenuItemActive', [$this, 'isMenuItemActive']),
            new TwigFunction('isUrlMenuItemActive', [$this, 'isUrlMenuItemActive']),
            new TwigFunction('addMenuItem', [$this, 'addItem']),
            new TwigFunction('newMenuItem', [$this, 'newItem']),
        ];
    }

    /**
     * @param array<array<mixed>>|null $routeParams
     * @param array<array<mixed>>|null $options
     * @param MenuItem[]|null          $subItems
     */
    public function newItem(
        string $id,
        ?string $label,
        ?string $route,
        ?array $routeParams = [],
        ?array $options = [],
        ?array $subItems = [],
    ): MenuItem {
        return new MenuItem($id, $label, $route, $routeParams, $options, $subItems);
    }

    public function addItem(string $menuName, MenuItem $item): MenuTwig
    {
        $item->url = $this->getUrl($item);

        if ($item->subItems) {
            foreach ($item->subItems as $subItem) {
                $subItem->url = $this->getUrl($subItem);
            }
        }
        $this->menu[$menuName]['items'][$item->label] = $item;

        return $this;
    }

    public function getItem(string $menuName, string $label): MenuItem|bool
    {
        return $this->menu[$menuName]['items'][$label] ?? false;
    }

    public function getUrl(MenuItem $item): ?string
    {
        if ($item->route) {
            return $this->urlGenerator->generate($item->route, $item->routeParams ?? []);
        }

        /** @var ?string */
        return $item->options['url'] ?? null;
    }

    public function isMenuItemActive(MenuItem $item): bool
    {
        $activeRoute = $this->request?->get('_route');
        if ($item->route && $activeRoute == $item->route) {
            return true;
        }
        $routes = $item->options['activeRoutes'] ?? false;
        if ($routes && is_iterable($routes)) {
            foreach ($routes as $route) {
                if ($activeRoute == $route) {
                    return true;
                }
            }
        }

        return false;
    }

    public function isUrlMenuItemActive(MenuItem $item): bool
    {
        return $item->url === $this->request?->getRequestUri();
    }

    /**
     * @return MenuItem[]
     */
    public function getMenuItems(string $nom): array
    {
        return $this->menu[$nom]['items'] ?? [];
    }

    /**
     * @return MenuItem[]|null
     */
    public function getMenuSubItems(MenuItem $item): ?array
    {
        return $item->subItems ?? [];
    }

    /**
     * @return array<mixed>
     */
    public function getMenuOptions(string $nom): array
    {
        return $this->menu[$nom]['options'] ?? [];
    }
}
