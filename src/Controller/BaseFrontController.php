<?php

namespace App\Controller;

use App\Menu\Menu;
use Psr\Log\LoggerInterface;

class BaseFrontController extends BaseController
{
public function __construct(Menu $menu, LoggerInterface $applicationLogger)
    {
        $this->menuTwig = $menu->menuTwig;
        $this->logger = $applicationLogger;
    }
}
