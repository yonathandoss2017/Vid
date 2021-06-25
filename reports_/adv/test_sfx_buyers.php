<?php
	
	$Json = json_decode(file_get_contents('https://sfx.freewheel.tv/api/inbound/buyer?token=1235953da965bb433a84a81e5fd6e482cc6a2e3b'));
	
	//print_r($Json);
	
	echo "ID,Name,Type\n";
	
	foreach($Json->results as $J){
		
		echo $J->id . "," . $J->name . "," . $J->type . "\n";
		
	}
