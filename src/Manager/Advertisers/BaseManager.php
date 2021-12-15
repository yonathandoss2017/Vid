<?php

namespace App\Report\Manager\Advertisers;

use SQL as Connection;

class BaseManager
{
    protected static $connection;

    public function __construct() {

    }

    protected static function getConnection() {
        if(static::$connection) {
            return static::$connection;
        }

        global $dbhost;
        global $dbAdvName;
        global $dbuser;
        global $dbpass;
        $connection = new Connection($dbhost, $dbAdvName, $dbuser, $dbpass);
        static::$connection = $connection;

        return static::$connection;
    }

    protected static function sanitize($param) {
        return mysqli_real_escape_string(static::getConnection()->link, $param);
    }
}