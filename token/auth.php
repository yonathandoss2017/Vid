<?php
require '/var/www/html/login/src/bootstrap.php';
use App\Report\App;
use App\Report\Http\Controller\JWTController;

(new App(new JWTController()))->run();
