<?php
require './../../src/bootstrap.php';

use App\Report\App;
use App\Report\Http\Controller\CampaignVMPInvestmentController;

(new App(new CampaignVMPInvestmentController()))->run();