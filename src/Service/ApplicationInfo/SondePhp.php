<?php

namespace App\Service\ApplicationInfo;

class SondePhp extends Lib
{
    public function execute(): void
    {
        $this->testDB($this->env['APP_DATABASE_URL'], 'SELECT * FROM parameter');
    }
}
