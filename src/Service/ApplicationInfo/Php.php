<?php

namespace App\Service\ApplicationInfo;

class Php extends Lib
{
    /**
     * @var string[]
     */
    public array $requiredextensions = [
        'Zend OPcache',
        'ctype',
        'curl',
        'dom',
        'fileinfo',
        'gd',
        'iconv',
        'intl',
        'json',
        'mbstring',
        'mysqli',
        'PDO',
        'SimpleXML',
        'soap',
        'tokenizer',
        'xml',
        'xmlwriter',
        'zip',
        'mysqli',
        'pdo_mysql',
        'xmlreader',
        'gd',
        'sodium',
    ];

    public function execute(): void
    {
        $this->resetCache();
        $this->testVersion();
        $this->testExtensions($this->requiredextensions);
        $this->envReport();
        $this->testDirectory();
        $this->testDB($this->env['APP_DATABASE_URL'], 'SELECT * FROM parameter');
    }
}
