<?php

namespace App\Controller;

use App\Menu\MenuBase;
use App\Menu\MenuTwig;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
{
    public MenuTwig $menuTwig;
    protected LoggerInterface $logger;

    public function __construct(MenuBase $menu, LoggerInterface $applicationLogger)
    {
        $this->menuTwig = $menu->menuTwig;
        $this->logger = $applicationLogger;
    }

    /**
     * Write a debug info in logger.
     *
     * @param mixed[] $context
     */
    public function logDebug(string $message, array $context = []): void
    {
        $this->logger->debug($message, $context);
    }

    /**
     * Write an info in logger.
     *
     * @param mixed[] $context
     */
    public function logInfo(string $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    /**
     * Write an error info in logger.
     *
     * @param mixed[] $context
     */
    public function logError(string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }

    /**
     * Write a warning info in logger.
     *
     * @param mixed[] $context
     */
    public function logWarning(string $message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }
}
