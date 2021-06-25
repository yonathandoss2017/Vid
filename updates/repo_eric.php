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
	
	header("Content-type: text/csv");
	header("Content-Disposition: attachment; filename=repogrupoindependiente.csv");
	header("Pragma: no-cache");
	header("Expires: 0");
	
	echo "Sitio,Dia,Revenue";
	echo "\n";
	$sql = "SELECT * FROM sites WHERE idUser = 370";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Site = $db->fetch_array($query)){
			$idSite = $Site['id'];
			
			for($I = 1; $I <= 31; $I++){
				echo $Site['siteurl'] . ",$I/12/2019";
				$sql = "SELECT SUM(Coste) FROM stats WHERE idSite = $idSite AND Date = '2019-12-$I'";
				echo "," . number_format($db->getOne($sql), 2, '.', '');
				echo "\n";
			}
			
		}
	}