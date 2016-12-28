<?php

$config['database'] = array(
    'default'       => 'mysql',

    'connections'   => array(

        'mysql' => array(
            'driver'    => 'mysql',
            // http://qiita.com/dolaemoso/items/35f6bba22801b4027ec4
            'host'      => isset($_SERVER['SLIM_HOST']) ? $_SERVER['SLIM_HOST'] : '',
            'database'  => isset($_SERVER['SLIM_NAME']) ? $_SERVER['SLIM_NAME'] : '',
            'username'  => isset($_SERVER['SLIM_USER']) ? $_SERVER['SLIM_USER'] : '',
            'password'  => isset($_SERVER['SLIM_PASS']) ? $_SERVER['SLIM_PASS'] : '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ),

        'sqlite' => array(
            'driver'   => 'sqlite',
            'database' => APP_PATH.'storage/db/database.sqlite',
            'prefix'   => '',
        ),

        'pgsql' => array(
            'driver'   => 'pgsql',
            'host'     => 'localhost',
            'database' => 'database',
            'username' => 'root',
            'password' => '',
            'charset'  => 'utf8',
            'prefix'   => '',
            'schema'   => 'public',
        ),

        'sqlsrv' => array(
            'driver'   => 'sqlsrv',
            'host'     => '127.0.0.1',
            'database' => 'database',
            'username' => 'user',
            'password' => '',
            'prefix'   => '',
        ),

    )
);