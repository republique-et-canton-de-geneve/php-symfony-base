<?php

namespace App\Menu;

class MenuBase
{
    public MenuTwig $menuTwig;

    public function __construct(MenuTwig $menuTwig)
    {
        $this->menuTwig = $menuTwig;
    }
}
