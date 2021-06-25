<?php
	
	$jsonTest = file_get_contents('json.test');
	
	$decoded_result = json_decode($jsonTest);
	print_r($decoded_result);
	
	
?>