<?php

return [
    'mariadb' => [
        // Required parameters
        'username' => 'root',
        'pass' => 'root',
        'driver' => 'server',
        'label' => 'Mariadb',
    ],
    'mysql' => [
        // Required parameters
        'username' => 'root',
        'pass' => 'root',
        'driver' => 'server',
        'label' => 'MySql',
    ],
    'postgres' => [
        // Required parameters
        'username' => 'user',
        'pass' => 'root',
        'driver' => 'pgsql',
        'label' => 'PostgreSQL',
        'mode' => "verify-full' sslrootcert='/var/www/html/certs/postgres/rootCA.crt' sslcert='/var/www/html/certs/postgres/client.crt' sslkey='/var/www/html/certs/postgres/client.key",
        //      'mode'   => "verify-full' sslrootcert='/var/certs/www-data/postgres/rootCA.crt' sslcert='/var/certs/www-data/postgres/client.crt' sslkey='/var/certs/www-data/postgres/client.key"
    ],
    'SQLite' => [
        // Required parameters
        'username' => 'user',
        'pass' => '*',
        'driver' => 'sqlite',
        'label' => 'SQLite',
        'databases' => '/var/www/data/database.sqlite',
    ],
];
