<?php
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	header("Allow: GET, POST, OPTIONS, PUT, DELETE");
	$method = $_SERVER['REQUEST_METHOD'];
	if($method == "OPTIONS") {
	    die();
	}
	
	if(isset($_GET['dspid'])){
		$DSPID = $_GET['dspid'];
		if(is_numeric($DSPID)){
			$BuyersJson = json_decode(file_get_contents('https://sfx.freewheel.tv/api/inbound/buyer?token=1235953da965bb433a84a81e5fd6e482cc6a2e3b'));
			//print_r($BuyersJson);
			
			foreach($BuyersJson->results as $ActiveBIds){
				//echo $DSPID;
				if($ActiveBIds->{'seat-id'} == $DSPID){
					echo 'OK';
					exit(0);
				}
				
			}
			
			echo 'KO';
		}else{
			echo 'Invalid DSP ID';
		}
	}else{
		echo 'Invalid DSP ID';
	}
	