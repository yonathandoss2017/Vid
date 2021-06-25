<?php
	
	/*	
		http://vidoomy.bc-us-east.bidswitch.net/vidoomy_bid
		http://vidoomy.bc-us-west.bidswitch.net/vidoomy_bid
		http://vidoomy.bc-eu.bidswitch.net/vidoomy_bid
		http://vidoomy.bc-apac-jp.bidswitch.net/vidoomy_bid	
	*/
	
	$h = fopen("country.csv", "r");
	//$C = fgetcsv($h, 1000, ",");
	
	while (($C = fgetcsv($h, 1000, ",")) !== FALSE){
		if($C[5] == 'Americas'){
			$DC = "https://vidoomy.bc-us-east.bidswitch.net/vidoomy_bid";
		}else{
			$DC = "https://vidoomy.bc-eu.bidswitch.net/vidoomy_bid";
		}
		
		$List[$C[1]] = $DC;
	}

	echo json_encode($List);