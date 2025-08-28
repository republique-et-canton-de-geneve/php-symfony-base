<?php

namespace App\Service\ApplicationInfo;

class Symfony extends Php
{
    public function execute(): void
    {
        $this->testVersion();
        $this->testExtensions($this->requiredextensions);
        $this->envReport();
        $this->testDirectory();
        $this->testDB($this->env['APP_DATABASE_URL'], 'SELECT * FROM parameter');
    }
}
