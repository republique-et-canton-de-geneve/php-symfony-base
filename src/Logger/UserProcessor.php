<?php

namespace App\Logger;

use Monolog\Attribute\AsMonologProcessor;
use Monolog\LogRecord;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Add login name in logger.
 */
#[AsMonologProcessor]
class UserProcessor
{
    private Security $security;
    private RequestStack $requestStack;

    public function __construct(Security $security, RequestStack $requestStack)
    {
        $this->security = $security;
        $this->requestStack = $requestStack;
    }

    // this method is called for each log record; optimize it to not hurt performance
    public function __invoke(LogRecord $record): LogRecord
    {
        $user = $this->security->getUser();
        $record->extra['user'] = $user?->getUserIdentifier();
        $record->extra['url'] = $this->requestStack->getCurrentRequest()?->getRequestUri();

        return $record;
    }
}
