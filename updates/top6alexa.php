<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');

	$db3 = new SQL($pubDev01['host'], $pubDev01['db'], $pubDev01['user'], $pubDev01['pass']);
	
	$sql = "SELECT DISTINCT(alexa_domain_id) AS alexa_domain_id FROM vidoomy.alexa_traffic_data ";
	$query = $db3->query($sql);
	if($db3->num_rows($query) > 0){
		while($W = $db3->fetch_array($query)){
			$idDom = $W['alexa_domain_id'];
			
			$sql = "SELECT COUNT(*) FROM alexa_traffic_data WHERE alexa_domain_id = '$idDom'";
			$Cant = $db3->getOne($sql);
			if($Cant > 5){
				//echo $idDom . '<br>';
				$Lim = $Cant - 5;
				
				echo $sql = "DELETE FROM alexa_traffic_data WHERE alexa_domain_id = '$idDom' ORDER BY users ASC LIMIT $Lim";
				echo "\n";
				$db3->query($sql);
			}
	
		}
	}
	/*
		
		SELECT alexa_traffic_data.rank as Rank, country.nicename as Country, alexa_traffic_data.page_views as PageViews, alexa_traffic_data.users as Users FROM alexa_traffic_data
INNER JOIN country on country.id = alexa_traffic_data.`country_id`
INNER JOIN alexa_domain on alexa_domain.id = alexa_traffic_data.alexa_domain_id
WHERE alexa_domain.url LIKE 'as.com'
		*/
?>