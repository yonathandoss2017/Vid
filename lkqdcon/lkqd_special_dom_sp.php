<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('config.php');
	require('constantes.php');
	require('db.php');
	require('common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	header("Content-type: text/csv");
    header('Content-Disposition: attachment; filename="rep_supplypartner_domain_formatloads.csv"');

	$decoded_result = json_decode(file_get_contents('rep.json'));
	
	//exit(0);
	foreach($decoded_result->data->entries as $entry){
		$LKQDid = $entry->fieldId;
		$formatLoads = $entry->formatLoads;
		$Domain = $entry->dimension2Name;
		
		echo "$LKQDid,$Domain,$formatLoads\n";
	}