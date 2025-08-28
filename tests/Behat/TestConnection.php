<?php

namespace App\Tests\Behat;

use Doctrine\DBAL\Driver\Connection as ConnectionInterface;
use Doctrine\DBAL\Driver\Middleware\AbstractConnectionMiddleware;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement;
use Exception;

class TestConnection extends AbstractConnectionMiddleware
{
    public function __construct(
        ConnectionInterface $connection,
    ) {
        parent::__construct($connection);
    }

    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function prepare(string $sql): Statement
    {
        /** @var array{'BEHAT_DOCTRINE_SQL_THROW_EXCEPTION':string|null} $GLOBALS */
        if (
            ($GLOBALS['BEHAT_DOCTRINE_SQL_THROW_EXCEPTION'] ?? false)
            && preg_match($GLOBALS['BEHAT_DOCTRINE_SQL_THROW_EXCEPTION'], $sql)
        ) {
            throw new Exception('BHEAT DOCTRINE THROW EXCEPTION');
        }

        return parent::prepare($sql);
    }

    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function query(string $sql): Result
    {
        /** @var array{'BEHAT_DOCTRINE_SQL_THROW_EXCEPTION':string|null} $GLOBALS */
        if (
            ($GLOBALS['BEHAT_DOCTRINE_SQL_THROW_EXCEPTION'] ?? false)
            && preg_match($GLOBALS['BEHAT_DOCTRINE_SQL_THROW_EXCEPTION'], $sql)
        ) {
            throw new Exception('BHEAT DOCTRINE THROW EXCEPTION');
        }

        return parent::query($sql);
    }

    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function exec(string $sql): int|string
    {
        /** @var array{'BEHAT_DOCTRINE_SQL_THROW_EXCEPTION':string|null} $GLOBALS */
        if (
            ($GLOBALS['BEHAT_DOCTRINE_SQL_THROW_EXCEPTION'] ?? false)
            && preg_match($GLOBALS['BEHAT_DOCTRINE_SQL_THROW_EXCEPTION'], $sql)
        ) {
            throw new Exception('BHEAT DOCTRINE THROW EXCEPTION');
        }

        return parent::exec($sql);
    }
}
