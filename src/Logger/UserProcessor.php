<?php

namespace App\Logger;

use Monolog\Attribute\AsMonologProcessor;
use Monolog\LogRecord;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Add login name in logger.
 */
#[AsMonologProcessor]
class UserProcessor
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    // this method is called for each log record; optimize it to not hurt performance
    public function __invoke(LogRecord $record): LogRecord
    {
        $user = $this->security->getUser();
        $record->extra['user'] = $user?->getUserIdentifier();

        return $record;
    }
}
