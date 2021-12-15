<?php
require './../src/bootstrap.php';

use App\Report\App;
use App\Report\Http\Controller\JWTController;

(new App(new JWTController()))->run();
