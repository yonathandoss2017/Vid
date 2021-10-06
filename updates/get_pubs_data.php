<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/common.lib.php');

	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	$db2 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);
	
		$Pubs[] = 'ComunicaloSA';
	$Pubs[] = 'Adbite';
	$Pubs[] = 'MotorES';
	$Pubs[] = 'Kech24';
	$Pubs[] = 'WarnerMedia';
	$Pubs[] = 'UTHRUSSIA';
	$Pubs[] = 'Losinformativos';
	$Pubs[] = 'admin@ecuadornoticias.com';
	$Pubs[] = 'Allwomenstalk';
	$Pubs[] = 'ezoic';
	$Pubs[] = 'GrupoOLX';
	$Pubs[] = 'grupoep';
	$Pubs[] = 'sk-knower';
	$Pubs[] = 'ParaphraseOnline';
	$Pubs[] = 'Diariomejor';
	$Pubs[] = 'zonamista';
	$Pubs[] = 'LahoraPE';
	$Pubs[] = 'Rostrosvenezolanos';
	$Pubs[] = 'BusinessLuxuryMmx';
	$Pubs[] = 'Alfredoalvarez';
	$Pubs[] = 'Comidaschilenas.com';
	$Pubs[] = 'Yoviajocr';
	$Pubs[] = 'Larotativa';
	$Pubs[] = 'knignitskiy@gmail.com';
	$Pubs[] = 'Noreste';
	$Pubs[] = 'Alertageekchile';
	$Pubs[] = 'LetraRojaMx';
	$Pubs[] = 'Orientation-chabab';
	$Pubs[] = 'GrupoTPP';
	$Pubs[] = 'jaweather';
	$Pubs[] = 'SanCarlosDig';
	$Pubs[] = 'kalilahreynolds';
	$Pubs[] = 'jovempan';
	$Pubs[] = 'Informateprimero';
	$Pubs[] = 'f1only';
	$Pubs[] = 'lettresgratuites';
	$Pubs[] = 'Youtvrs';
	$Pubs[] = 'InspanjeNl';
	$Pubs[] = 'Sienaclub';
	$Pubs[] = 'StileTV';
	$Pubs[] = 'Monza-news';
	$Pubs[] = 'IlnotiziarioIT';
	$Pubs[] = 'Agrigentooggi';
	$Pubs[] = 'Gnius';
	$Pubs[] = 'Maggioliadv';
	$Pubs[] = 'Dolcipassioni';
	$Pubs[] = 'CascinaNotizie';
	$Pubs[] = 'sicesi';
	$Pubs[] = 'MondoSportivo';
	$Pubs[] = 'DailyBaseNl';
	$Pubs[] = 'Ilgiornaledeimarinai';
	$Pubs[] = 'TerniinreteIT';
	$Pubs[] = 'LavocedimanduriaIT';
	$Pubs[] = 'Volyn';
	$Pubs[] = 'Patrioty';
	$Pubs[] = 'Tepka';
	$Pubs[] = 'Up2DigitalPt';
	$Pubs[] = 'Madvertise';
	$Pubs[] = 'Labtv';
	$Pubs[] = 'Shoppable';
	$Pubs[] = 'TkoPl';
	$Pubs[] = 'SevHolding';
	$Pubs[] = 'CamperOnlineIT';
	$Pubs[] = 'Menslife';
	$Pubs[] = 'Sport&Co';
	$Pubs[] = 'DucklabIT';
	
	foreach($Pubs as $Pub){
		$sql = "SELECT user.email AS Email, country.nicename AS Country, finance_account.currency_id AS Currency FROM user
		INNER JOIN publisher ON publisher.user_id = user.id
		INNER JOIN finance_account ON finance_account.id = publisher.finance_account_id
		INNER JOIN country ON country.id = publisher.country_id
		WHERE username = '$Pub'";
		//exit();
		$query = $db2->query($sql);
		
		if($db->num_rows($query) > 0){
		
			$Da = $db2->fetch_array($query);
			
			if($Da['Currency'] == 2) {
				$Currency = 'Euro';
			}else{
				$Currency = 'Dolar';
			}
			
			echo '"' . $Pub . '","' . $Da['Email'] . '","' . $Da['Country'] . '","' . $Currency . '"' . "\n";
		}else{
			echo '"' . $Pub . '","","",""' . "\n";
		}
	}
	