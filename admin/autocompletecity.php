<?php
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('../config.php');
	require('../constantes.php');
	require('../db.php');
	require('../common.lib.php');
	//require('countries.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	
	
	if(@$_SESSION['Admin'] >= 1){
		$search = my_clean($_GET['query']);
		$cCode = my_clean($_GET['c']);
		
		$sql = "SELECT id FROM countries WHERE country_code = '$cCode' LIMIT 1";
		$idC = $db->getOne($sql);
		
		echo '{
	"query": "'.$search.'"';
		echo ', "suggestions": [';
		$sql = "SELECT * FROM cities WHERE idCountry = '$idC' AND Name LIKE '$search%'";
		$query = $db->query($sql);
		if($db->num_rows($query) > 0){
			
			
			$c = '';
			while($Ci = $db->fetch_array($query)){
				
				echo $c . '{ "value": "' . $Ci['Name'] . '", "data": "' . $Ci['Code'] . '" }';
				$c = ',';
			}
			
			
		}
		echo ']';
		echo '}';
		
	}
?>