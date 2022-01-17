<?php
require './../../src/bootstrap.php';
//$requestHeaders = apache_request_headers();
//var_dump($requestHeaders);
//exit(0);
use App\Report\App;
use App\Report\Http\Controller\CampaignActiveVMPController;

(new App(new CampaignActiveVMPController()))->run();
