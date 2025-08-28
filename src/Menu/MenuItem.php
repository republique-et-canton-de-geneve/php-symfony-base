<?php

namespace App\Menu;

class MenuItem
{
    public string $id;
    public string $label;
    public ?string $route;
    /**
     * @var array<mixed>|null
     */
    public ?array $routeParams;
    public ?string $url;
    /**
     * @var array<mixed>|null
     */
    public ?array $options;
    /**
     * @var MenuItem[]|null
     */
    public ?array $subItems;

    /**
     * Make an item for the menu.
     *
     * @param string            $id          id of menu item
     * @param string|null       $label       label of menu item
     * @param string|null       $route       route of menu item
     * @param array<mixed>|null $routeParams route parameters of the menu item
     * @param array<mixed>|null $options     options of item
     * @param MenuItem[]|null   $subItems    list of subItems
     */
    public function __construct(
        string $id,
        ?string $label = null,
        ?string $route = null,
        ?array $routeParams = [],
        ?array $options = [],
        ?array $subItems = [],
    ) {
        $this->id = $id;
        $this->label = $label ?: $id;
        $this->route = $route;
        $this->routeParams = $routeParams;
        $this->options = $options;
        $this->subItems = $subItems;
    }
}
