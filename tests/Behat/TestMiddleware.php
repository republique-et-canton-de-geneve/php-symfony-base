<?php

namespace App\Tests\Behat;

use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\Middleware as MiddlewareInterface;

class TestMiddleware implements MiddlewareInterface
{
    public function wrap(Driver $driver): Driver
    {
        return new TestDriver($driver);
    }
}
