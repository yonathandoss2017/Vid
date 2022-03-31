<?php

namespace App\Report\Http\Controller;

use App\Report\Http\Response;
use App\Report\Security\Auth;
use App\Report\Security\JWTAuth;
use DateInterval;
use DateTime;

class JWTController extends Controller
{
    /**
     * Execute the POST request
     *
     * @return Response
     */
    public function post()
    {
        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            return Response::unauthorized();
        }

        $user = $_SERVER['PHP_AUTH_USER'];
        $password = $_SERVER['PHP_AUTH_PW'];
        $user = (new Auth())->login($user, $password);

        if (!$user) {
            return Response::unauthorized();
        }

        $jwtAuth = new JWTAuth($_ENV['JWT_VMP_SECRET']);

        $expireAt = new DateTime();
        $expireAt->add(new DateInterval('P10Y'));

        $token = $jwtAuth->sign([
            'user_id' => $user['id']
        ], $expireAt->getTimestamp());

        Response::json(json_encode(compact('token')));
    }
}
