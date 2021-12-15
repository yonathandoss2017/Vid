<?php

namespace App\Report\Security;

use App\Report\Manager\Advertisers\UserManager;

class Auth
{
    public function __construct() {
        $this->userManager = new UserManager();
    }

    public function login(string $username, string $password)
    {
        $user =  $this->userManager->getByUsername($username);
        
        if(! $user || !password_verify($password, $user['password'])) {
            return null;
        }

        unset($user['password']);
        return $user;
    }
}