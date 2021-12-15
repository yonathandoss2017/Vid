<?php

namespace App\Report;

use App\Report\Http\Controller\Controller;

class App
{
    private $controller;

    public function __construct(Controller $controller) {
        $this->controller = $controller;
    }

    public function run()
    {
        $this->controller->run();
    }
}