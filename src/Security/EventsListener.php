<?php

namespace App\Security;

use App\Application;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class EventsListener
{
    protected Application $application;
    protected LoggerInterface $logger;

    public function __construct(Application $application, LoggerInterface $applicationLogger)
    {
        $this->application = $application;
        $this->logger = $applicationLogger;
    }


    #[AsEventListener()]
    public function loginSuccessEvent(LoginSuccessEvent $event): void
    {
        if (!$event->getPreviousToken() || (EnvAuthenticator::class !== get_class($event->getAuthenticator()))) {
            /** @var User $user */
            $user = $event->getUser();
            $this->logger->info('Success login', $user->getAttributes());
        }
    }

    #[AsEventListener()]
    public function loginFailureEvent(LoginFailureEvent $event): void
    {
        /** @var ?User $user */
        $user = $event->getPassport()?->getUser();
        $this->logger->info('Login failure', $user ? $user->getAttributes() : []);
    }

    #[AsEventListener()]
    public function checkPassportEvent(CheckPassportEvent $event): void
    {
        /**
         * It is possible here to determine roles by other means such as db.
         */
        /** @var User $user */
        $user = $event->getPassport()->getUser();
        // guarantee every user at least has ROLE_USER
        $roles = ['ROLE_USER' => 'ROLE_USER'];
        $ginaRoles = $user->getGinaRoles();
        $lenPrefixSamlRole = mb_strlen($this->application->getGinaRolePrefix());
        foreach ($ginaRoles as $role) {
            $role = mb_strtoupper(strval($role));
            if (0 === strncmp($role, $this->application->getGinaRolePrefix(), $lenPrefixSamlRole)) {
                $name = 'ROLE_' . mb_substr($role, $lenPrefixSamlRole);
                $roles[$name] = $name;
            }
        }
        $user->setRoles($roles);
    }
}
