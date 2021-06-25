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
	mysqli_set_charset($db->link,'utf8');
	
	$CurrentJson = json_decode(file_get_contents("https://www.vidoomy.com/sellers.json"));
	
	foreach($CurrentJson->sellers as $Key => $Val){
		$Domain = $Val->domain;
		$sellerId = $Val->seller_id;
		$Name = $Val->name;
		$SellerType = $Val->seller_type;
		
		$sql = "SELECT company FROM users INNER JOIN sites ON sites.idUser = users.id WHERE sites.siteurl LIKE '%$Domain%' AND sites.deleted = 0 LIMIT 1";
		$Company = $db->getOne($sql);
		
		//$Company = utf8_decode($Company);
		
		$NewSellers[$Key] = array(
			'seller_id' 	=> 	$sellerId,
			'name'			=>	$Company,
			'domain'		=>	$Domain,
			'seller_type'	=>	$SellerType
		);
	}
	
	//print_r($NewSellers);
	
	$CurrentJson->sellers = $NewSellers;
	
	header('Content-Type: application/json'); 
	
	echo json_encode($CurrentJson, JSON_PRETTY_PRINT);
?>