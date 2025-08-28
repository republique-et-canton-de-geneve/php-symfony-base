<?php

namespace App\Listener;

use App\Application;
use App\Parameter;
use App\Security\Role;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class KernelRequestListener
{
    /** @var UrlGeneratorInterface */
    protected $urlGenerator;

    protected Parameter $parameter;

    protected Application $application;

    protected Security $security;
    protected Environment $twig;

    public function __construct(
        Parameter $parameter,
        Application $application,
        Security $security,
        Environment $twig,
    ) {
        $this->parameter = $parameter;
        $this->application = $application;
        $this->security = $security;
        $this->twig = $twig;
    }

    #[AsEventListener()]
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        // test le mode maintenance doit être affiché
        if (
            $this->parameter->modeMaintenance
            && ('saml_login' != $request->attributes->get('_route'))
            && !$this->security->isGranted(Role::ADMINISTRATEUR)
        ) {
            throw new ServiceUnavailableHttpException();
        }
    }
}
