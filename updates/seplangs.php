<?php
	$Langs['ES'] = array();
	$Langs['EN'] = array();
	$Langs['NA'] = array();
	$MigrationPubs = array();
	
	$handle = fopen("lang_pubs_09072020.txt", "r");
	if ($handle) {
	    while (($line = fgets($handle)) !== false) {
		    $arLi = explode('-', $line);
		    
		    $Langs[trim($arLi[1])][] = trim($arLi[0]);
			
	    }
	    fclose($handle);
	} else {
	    echo "error opening the file.";
	} 
	/*
	$handle = fopen("mig_pubs_31052020.txt", "r");
	if ($handle) {
	    while (($line = fgets($handle)) !== false) {
		    $arL = explode('Pub:', $line);
		    $PubUser = trim($arL[1]);
			
			$MigrationPubs[] = $PubUser;
	    }
	    fclose($handle);
	} else {
	    echo "error opening the file.";
	} 
	*/
	echo "Pubs ES:\n";
	foreach($Langs['ES'] as $Pub){
		//if(in_array($Pub, $MigrationPubs)){
			echo $Pub . "\n";
		//}
	}
	
	echo "\n\nPubs EN:\n";
	foreach($Langs['EN'] as $Pub){
		//if(in_array($Pub, $MigrationPubs)){
			echo $Pub . "\n";
		//}
	}
	
	
	echo "\n\nPubs NA:\n";
	foreach($Langs['NA'] as $Pub){
		//if(in_array($Pub, $MigrationPubs)){
			echo $Pub . "\n";
		//}
	}