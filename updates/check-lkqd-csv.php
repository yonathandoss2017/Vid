<?php

	$csvData = array_map('str_getcsv', file('LKQD-Report-2022-01-12-121106.csv'));
	
	$Impressions = 0;
	
	foreach($csvData as $Data){
		if(array_key_exists(6, $Data)){
			//print_r($Data);
			//echo $Data[6];
			//exit(0);
			if($Data[6] != 'Impressions'){
				$Impressions += $Data[6];
			}
		}
	}
	
	echo $Impressions;