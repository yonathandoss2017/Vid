<?php
	$csvUsers = array_map('str_getcsv', file('ssp_users.csv'));
	
	$Users = array();
	
	foreach($csvUsers as $U){
		$Users[$U[0]] = $U[7];
	}
	
	foreach($csvData as $Data){
		$Users[$Data[0]] = $Data[7];
	}
	
	$csvRepo = array_map('str_getcsv', file('query-4e3a6966-cc30-4c52-b3a1-97b0fe738ecc.csv'));
	
	$N = 0;
	foreach($csvRepo as $R){
		if($N > 0){
			echo '"' . $Users[$R[0]] . '","' . 
			number_format($R[1], 0, '.', ',') . '","' . 
			number_format($R[2], 0, '.', ',') . '","' . 
			number_format($R[3], 0, '.', ',') . '","$' . 
			number_format($R[4], 2, '.', ',')  . '","$' . 
			number_format($R[5], 2, '.', ',') . '","$' . 
			number_format($R[6], 2, '.', ',') . '"' . 
			"\n";

		}

		$N++;
	}