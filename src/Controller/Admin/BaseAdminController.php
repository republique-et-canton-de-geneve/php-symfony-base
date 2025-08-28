<?php

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Menu\MenuAdmin;
use Psr\Log\LoggerInterface;

class BaseAdminController extends BaseController
{
    public function __construct(MenuAdmin $menu, LoggerInterface $applicationLogger)
    {
        $this->menuTwig = $menu->menuTwig;
        $this->logger = $applicationLogger;
    }
}
