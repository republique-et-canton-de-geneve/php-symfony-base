<?php

namespace App\Listener;

use App\Application;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Twig\Environment;

class ExceptionListener
{
    protected Environment $twig;
    protected Application $application;
    protected LoggerInterface $logger;

    public function __construct(Environment $twig, Application $application, LoggerInterface $applicationLogger)
    {
        $this->twig = $twig;
        $this->application = $application;
        $this->logger = $applicationLogger;
    }

    #[AsEventListener()]
    public function onKernelException(ExceptionEvent $event): void
    {
        if (
            ('prod' === $this->application->getEnvironment() || 'test' === $this->application->getEnvironment())
            && !$this->application->isDebug()
        ) {
            if ($event->getThrowable() instanceof NotFoundHttpException) {
                $view = $this->twig->render('bundles/TwigBundle/Exception/error404.html.twig');
                $this->logger->debug('Page not found', ['message' => $event->getThrowable()->getMessage()]);
                $response = new Response($view, 404);
            } elseif ($event->getThrowable() instanceof AccessDeniedHttpException) {
                $view = $this->twig->render('bundles/TwigBundle/Exception/error403.html.twig');
                $this->logger->debug('Page access denied', ['message' => $event->getThrowable()->getMessage()]);
                $response = new Response($view, 403);
            } elseif ($event->getThrowable() instanceof ServiceUnavailableHttpException) {
                $view = $this->twig->render('bundles/TwigBundle/Exception/error503.html.twig');
                $this->logger->error(
                    'Error 503',
                    [
                        'message' => $event->getThrowable()->getMessage(),
                        'file' => $event->getThrowable()->getFile(),
                        'line' => $event->getThrowable()->getLine(),
                    ]
                );
                $response = new Response($view, 503);
            } else {
                $view = $this->twig->render('bundles/TwigBundle/Exception/error500.html.twig');
                $this->logger->error(
                    'Error 500',
                    [
                        'message' => $event->getThrowable()->getMessage(),
                        'file' => $event->getThrowable()->getFile(),
                        'line' => $event->getThrowable()->getLine(),
                    ]
                );
                $response = new Response($view, 500);
            }
            $event->setResponse($response);
        }
    }
}
