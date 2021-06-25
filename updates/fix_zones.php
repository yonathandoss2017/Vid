<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 1);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/common.lib.php');
	require '/var/www/html/site/include/PHPMailer/PHPMailerAutoload.php';
	require('/var/www/html/login/admin/lkqdimport/common.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$dbuser2 = "root";
		$dbpass2 = "123123123";
		$dbhost2 = "aa12gqfb9qs8z09.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
		$dbname2 = "vidoomy";
		$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	$sql = "SELECT a.* FROM website_zone a JOIN (SELECT name, COUNT(*) FROM website_zone GROUP BY name HAVING count(*) > 1 ) b ON a.name = b.name ORDER BY a.name";
	
	$query = $db2->query($sql);
	while($S = $db2->fetch_array($query)){
		$idTag = $S['id'];
		
		if(strpos($S['name'], '_dt') === false && strpos($S['name'], '_mw') === false && strpos($S['name'], '_desktop') === false && strpos($S['name'], '_mobile') === false){
			if($S['platform'] == 1){
				$MD = '_dt';
			}elseif($S['platform'] == 2){
				$MD = '_mw';
			}else{
				$MD = '';
			}
			
			$NewTagname = $S['name'] . $MD;
			
			$sql = "UPDATE website_zone SET name = '$NewTagname' WHERE id = '$idTag' LIMIT 1";
			echo $sql . "<br/>";
			$db2->query($sql);
		}
	}