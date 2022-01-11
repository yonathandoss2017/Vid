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
        return $_SERVER["Authorization"] ?? $_SERVER["HTTP_AUTHORIZATION"] ?? '';
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
