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
	
	
	mysqli_set_charset($db->link,'utf8');
	mysqli_set_charset($db2->link,'utf8');
	
	
	$sql = "SELECT * FROM website ORDER BY id ASC";
	$query = $db2->query($sql);
	if($db2->num_rows($query) > 0){
		
		while($W = $db2->fetch_array($query)){
			$idSite = $W['id'];
			$NewUrl = '';
			
			if(substr($W['url'], 0, 4) != 'http'){
				$NewUrl .= 'https://';
			}
			
			$NewUrl .= strtolower($W['url']);
			
			if(substr($W['url'], -1, 1) != '/'){
				
				$NewUrl .= '/';
				
			}
			
			echo $NewUrl . '<br/>';
			
			$sql = "UPDATE website SET url = '$NewUrl' WHERE id = '$idSite' LIMIT 1";
			$db2->query($sql);
		}
		
	}