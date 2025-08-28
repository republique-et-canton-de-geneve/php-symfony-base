<?php

namespace App\Tests\Behat;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Driver\Middleware\AbstractDriverMiddleware;

class TestDriver extends AbstractDriverMiddleware
{
    /**
     * @throws Exception
     */
    public function connect(array $params): Connection
    {
        $connection = parent::connect($params);

        return new TestConnection($connection);
    }
}
