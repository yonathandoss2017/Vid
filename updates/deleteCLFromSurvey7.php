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
	
	require('/var/www/html/ads/MaxMind-DB-Reader-php-master/autoload.php');
	require_once '/var/www/html/ads/MaxMind-DB-Reader-php-master/src/MaxMind/Db/Reader.php';
	require_once '/var/www/html/ads/MaxMind-DB-Reader-php-master/src/MaxMind/Db/Reader/Decoder.php';
	require_once '/var/www/html/ads/MaxMind-DB-Reader-php-master/src/MaxMind/Db/Reader/InvalidDatabaseException.php';
	require_once '/var/www/html/ads/MaxMind-DB-Reader-php-master/src/MaxMind/Db/Reader/Metadata.php';
	use MaxMind\Db\Reader;
	
	$databaseFile = '/var/www/html/ads/MaxMind/GeoIP2-Country.mmdb';
	$reader = new Reader($databaseFile);
	

	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	$db2 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);
	
	mysqli_set_charset($db->link,'utf8');
	mysqli_set_charset($db2->link,'utf8');
	
	$sql = "SELECT * FROM surveys WHERE Campaign = 7"; //
	$query = $db->query($sql);
	while($R = $db->fetch_array($query)){
		$idR = $R['id'];
		
		$Data = $reader->get($R['IP']);
		$Country = '';
		if(is_array($Data)){
			if(array_key_exists('country', $Data)){
				$Country = $Data['country']['iso_code'];
			}
		}
		
		if($Country == 'MX'){
			echo $Country . ": ";
			echo $sql = "DELETE FROM surveys WHERE Campaign = 7 AND id = $idR"; //
			$db->query($sql);
			echo "\n";
		}
		
	}
	
	
	//migrateUser(1058);
