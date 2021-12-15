<?php

namespace App\Report\Manager\Advertisers;

class UserManager extends BaseManager
{
    public static function getById($userId) {
        $userId = static::sanitize($userId);
        
        $sql = "SELECT * FROM user WHERE id = $userId";

        return static::getConnection()->getFirst($sql);
    }

    public static function isUserExternalClient($userId, $client) {
        $userId = static::sanitize($userId);
        $client = static::sanitize($client);

        $sql = "SELECT count(id) FROM user_external_client 
            WHERE user_id = $userId 
            AND client = '$client'
            AND active = true";

        return static::getConnection()->getOne($sql);
    }

    public static function getByUsername($username) {
        $username = static::sanitize($username);
        $sql = "SELECT * FROM user WHERE username = '$username'";
        return static::getConnection()->getFirst($sql);
    }
}