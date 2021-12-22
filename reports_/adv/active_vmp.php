<?php
require './../../src/bootstrap.php';

use App\Report\App;
use App\Report\Http\Controller\CampaignActiveVMPController;

(new App(new CampaignActiveVMPController()))->run();