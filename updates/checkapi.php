<?php
	
	$JSON = file_get_contents('/var/www/html/login/updates/ssp_api_response2.json');
	
	$Arr = json_decode($JSON);
	
	$TotalRev = 0;
	
	foreach($Arr as $Reg){
		$TotalRev += $Reg->metrics->revenue;
	}
	
	echo $TotalRev;
	