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
	
	$Pubs[] = 'ESIMEDIA2';
	$Pubs[] = 'SDMGroup22';
	$Pubs[] = 'Fazendoanossafesta';
	$Pubs[] = 'Amandocozinhar';
	$Pubs[] = 'mauro@goodlymedia.com';
	$Pubs[] = 'oneirokriths123';
	$Pubs[] = 'AdcortoAgency';
	$Pubs[] = 'freelist.gr';
	$Pubs[] = 'Maisesports';
	$Pubs[] = 'Tenaxsoft';
	$Pubs[] = 'jameeltips';
	$Pubs[] = 'topviral';
	$Pubs[] = 'rubezhanskiy@modesco.ru';
	$Pubs[] = 'MaricaInfo';
	$Pubs[] = 'DoonHorizonIN';
	$Pubs[] = 'DKodingMedia';
	$Pubs[] = 'tctelevision';
	$Pubs[] = 'EnterCo';
	$Pubs[] = 'xrysessyntages.gr';
	$Pubs[] = 'NacionalMatanza';
	$Pubs[] = 'Radioprogressodeijui';
	$Pubs[] = 'ZinetMediaGroup';
	$Pubs[] = 'TheLiveNagpurIn';
	$Pubs[] = 'Khaosod1';
	$Pubs[] = 'RedUno';
	$Pubs[] = 'TEST_NO_TOCAR';
	$Pubs[] = 'G4trader';
	$Pubs[] = 'Pilotandofogao';
	$Pubs[] = 'LÍNEA.ORG';
	$Pubs[] = 'wikiwiki.jp';
	$Pubs[] = 'fatoscuriososclub';
	$Pubs[] = 'agenciasertaobr';
	$Pubs[] = 'qriswelljq@pildorasdefe.net';
	$Pubs[] = 'FVDigitalDO';
	$Pubs[] = 'KashmirPulseIn';
	$Pubs[] = 'matos@radioturquesa.fm';
	$Pubs[] = 'admin@easydest.com';
	$Pubs[] = 'BloombergquintIN';
	$Pubs[] = 'oradesibiu2022';
	$Pubs[] = 'BrujulaDigital';
	$Pubs[] = 'BeloudStudios';
	$Pubs[] = 'AbcrNewsIn';
	$Pubs[] = 'GrupodeMediosRD';
	$Pubs[] = 'psdmediahouse';
	$Pubs[] = 'IBGNewsIn';
	$Pubs[] = 'Clave300';
	$Pubs[] = 'NewsTime';
	$Pubs[] = 'BNNoticias';
	$Pubs[] = 'eladarissi88@gmail.com';
	$Pubs[] = 'LinkitBolivia';
	$Pubs[] = 'iniciaseo@gmail.com';
	$Pubs[] = 'Trendnieuws.nl';
	$Pubs[] = 'ATMedia';
	$Pubs[] = 'KODMediaHU';
	$Pubs[] = 'jumk';
	$Pubs[] = 'funpot';
	$Pubs[] = 'Quonomy';
	$Pubs[] = 'ZeneszovegHu';
	$Pubs[] = 'Leestips';
	$Pubs[] = 'CrowdMedia';
	$Pubs[] = 'LubieGrac';
	$Pubs[] = '5min';
	$Pubs[] = 'ObservatorulphRO';
	$Pubs[] = 'SecretMedia';
	$Pubs[] = 'Comparic';
	$Pubs[] = 'paokmania';
	$Pubs[] = 'JobbMintATv';
	$Pubs[] = 'todoig';
	$Pubs[] = 'AdsInteractiveHU';
	$Pubs[] = 'Legia.net';
	$Pubs[] = 'DzienDobryBelchatow';
	$Pubs[] = 'españafascinante';
	
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
	