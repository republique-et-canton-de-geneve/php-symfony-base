<?php
return [
    'mariadb' => array(
        // Required parameters
        'username'  => 'root',
        'pass'      => 'root',
        'driver'   => 'server',
        'label'     => 'Mariadb',
    ),
    'mysql' => array(
        // Required parameters
        'username'  => 'root',
        'pass'      => 'root',
        'driver'   => 'server',
        'label'     => 'MySql',
    ),
    'postgres' => array(
        // Required parameters
        'username'  => 'root',
        'pass'      => 'root',
        'driver'   => 'pgsql',
        'label'     => 'PostgreSQL',
    ),
 'SQLite' => array(
        // Required parameters
        'username'  => 'root',
        'pass'      => '*',
        'driver'   => 'sqlite',
        'label'     => 'SQLite',
        'databases' => '/var/www/data/database.sqlite'
    ),

];