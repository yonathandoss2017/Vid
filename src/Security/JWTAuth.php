<?php

namespace App\Report\Security;

use Firebase\JWT\JWT;

class JWTAuth
{
    private $secret;
    private $encrypt;

    public function __construct(string $secret, array $encrypt = ['HS256']) {
        $this->secret = $secret;
        $this->encrypt = $encrypt;
    }

    public function sign($data, $exp = null)
    {
        if(!$exp) {
            $exp = time() + (60*60);
        }

        $aud = $this->aud();
        return JWT::encode(compact('exp', 'aud', 'data'), $this->secret);
    }

    public function check(string $token)
    {
        if(empty($token))
        {
            throw new \UnexpectedValueException("Invalid token supplied.");
        }

        $decoded = $this->decode(
            $token,
            $this->secret,
            $this->encrypt
        );

        if($decoded->aud !== $this->aud())
        {
            throw new \UnexpectedValueException("Invalid user logged in.");
        }

        return $decoded;
    }

    public function decode(string $token)
    {
        return JWT::decode(
            $token,
            $this->secret,
            $this->encrypt
        );
    }

    public function data($token)
    {
        return $this->decode($token)->data;
    }

    private function aud()
    {
        $aud = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud);
    }

    public static function getAuthorizationHeader() {
        //return $_SERVER["Authorization"] ?? $_SERVER["HTTP_AUTHORIZATION"] ?? '';
   	$headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    }
    else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        //print_r($requestHeaders);
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    return $headers; 
    }

    public static function getRequestToken() {
        $authorization = static::getAuthorizationHeader();
        $tokens = explode(' ', $authorization);
        return $tokens[1] ?? '';
    }

    public function decodeCurrentRequest() {
        return $this->check(static::getRequestToken())->data;;
    }
}
