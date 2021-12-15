<?php

namespace App\Report\Http;

class Response
{
    public static function unauthorized() {
        static::header('HTTP/1.0 401', 'Unauthorized');
    }

    public static function methodNotAllowed() {
        static::header('HTTP/1.0 405', 'Method not allowed');
    }

    public static function header($header, $message = '') {
        header($header);
        echo $message;
    }

    public static function success($data  = null) {
        static::header("HTTP/1.0 200", $data);
    }

    public static function json($data) {
        header('Content-Type: application/json; charset=utf-8');
        static::success($data);
    }

    public static function notFound($data  = null) {
        static::header("HTTP/1.0 404", $data);
    }

    public static function badRequest($data  = null) {
        static::header("HTTP/1.0 400", $data);
    }

    public static function internalServerError($data = 'Internal Server Error') {
        static::header("HTTP/1.0 500", $data);
    }
}