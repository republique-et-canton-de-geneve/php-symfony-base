<?php

namespace App\Controller;

use App\Menu\Menu;
use App\Menu\MenuTwig;
use Psr\Log\LoggerInterface;

class BaseFrontController extends BaseController
{
    public MenuTwig $menuTwig;
    protected LoggerInterface $logger;

    public function __construct(Menu $menu, LoggerInterface $applicationLogger)
    {
        parent::__construct($menu, $applicationLogger);
    }
}
