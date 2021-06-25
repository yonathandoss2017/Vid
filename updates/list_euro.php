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
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	echo 'Usuario,DBA,Email,Coste' . "\n";
	
	$sql = "SELECT * FROM users WHERE currency = 2 AND AccM != 9999 AND deleted = 0";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){		
		while($U = $db->fetch_array($query)){	
			$idU = $U['id'];
			
			echo $U['user'] . ',';
			echo $U['nick'] . ',';
			echo $U['email'] . ',';
			
			$sql = "SELECT SUM(Coste) FROM " . STATS . " WHERE Date BETWEEN '2019-10-01' AND '2019-10-31' AND idUser = '$idU'";
			echo '€' . number_format(correctCurrency($db->getOne($sql), 2), 2, '.', '');
			
			echo "\n";
		}
	}