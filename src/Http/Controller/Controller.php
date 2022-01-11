<?php

namespace App\Report\Http\Controller;

use App\Report\Exception\UnauthorizedException;
use App\Report\Http\Response;
use App\Report\Manager\Advertisers\UserManager;
use App\Report\Security\JWTAuth;
use DateTime;
use Exception;
use Rakit\Validation\Validator;

class Controller {
    protected $validator;

    public function __construct()
    {
        $this->validator = (new Validator());
    }

    public $methods = [
        'GET',
        'POST',
        'PUT',
        'DELETE',
    ];

    public function run() {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $this->get();
                break;
            case 'POST':
                $this->post();
                break;
            case 'PUT':
                $this->put();
                break;
            case 'DELETE':
                $this->delete();
                break;
            default:
                $this->methodNotImplemented();
        }
    }

    public function get() {
        return Response::methodNotAllowed();
    }

    public function post() {
        return Response::methodNotAllowed();
    }

    public function put() {
        return Response::methodNotAllowed();
    }

    public function delete() {
        return Response::methodNotAllowed();
    }

    protected function methodNotImplemented() {
        throw new \Exception('Method not implemented');
    }

    protected function getRequestPayload() {
        return json_decode(file_get_contents('php://input'), true) ?? $_POST;
    }

    /**
     * @return void
     * @throws UnexpectedValueException
     */
    protected function reviewExtraParams(array $allowedParams, $params) {
        $unAllowedParams = array_diff(array_keys($params), $allowedParams);
    
        if ($unAllowedParams) {
            throw new \UnexpectedValueException("Request contains invalid properties: " . implode(',', $unAllowedParams));
        }
    }

    /**
     * @return void
     * @throws UnexpectedValueException
     */
    protected function validatePayload(array $rules) {
        $params = $this->getRequestPayload();
        $this->reviewExtraParams(array_keys($rules), $params);

        $validation = $this->validator->make($params, $rules);
    
        $validation->validate();
    
        if ($validation->fails()) {
            $errors = $validation->errors()->toArray();
            throw new \UnexpectedValueException(json_encode(compact('errors')));
        }
    }

    protected function getDateTime(string $date, $default = null) {
        try {
            return new DateTime($date);
        } catch (Exception $ex) {
            return $default;
        }
    }

    /**
     * @return void
     * @throws UnauthorizedException|UnexpectedValueException
     */
    protected function checkJWTAuthorization($client = 'VMP') {
        $jwtAuth = new JWTAuth($_ENV['JWT_VMP_SECRET']);
        $user = $jwtAuth->decodeCurrentRequest();
    
        if(!$user || !$user->user_id) {
            throw new UnauthorizedException();
        }

        $user = UserManager::getById($user->user_id);

        if(!$user) {
            throw new UnauthorizedException();
        }
    
        $userIsActive = UserManager::isUserExternalClient($user['id'], $client);
    
        if(!$userIsActive) {
            throw new UnauthorizedException();
        }
    }
}
