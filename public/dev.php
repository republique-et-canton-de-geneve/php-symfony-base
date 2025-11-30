<?php

use App\Kernel;

umask(0);
require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

return function (array $context): Kernel {
    return new Kernel('dev', true);
};
