<?php
require './../../src/bootstrap.php';
use App\Report\Http\Controller\CampaignVMPInvestmentController;
$controller = new CampaignVMPInvestmentController();
$controller->run();