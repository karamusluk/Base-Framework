<?php


namespace Models;

use Illuminate\Database\Capsule\Manager as Capsule;


class Database {

    // Data will be moved to .env file
    function __construct() {
        $capsule = new Capsule;
        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => '',
            'username'  => '',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);
        // Setup the Eloquent ORM…
        $capsule->bootEloquent();
    }

}