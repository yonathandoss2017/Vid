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
	
	$dbuser2 = "root";
	$dbpass2 = "Jz8eDbamcNx3TskWzrjzH7g";
	$dbhost2 = "vidoomy-production.cpijmqdfbof9.eu-west-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	$Pubs[] = 'hongkong01';
	$Pubs[] = 'Sahafahn';
	$Pubs[] = 'amqueretaro';
	$Pubs[] = 'entertainment.ie';
	$Pubs[] = 'Beaherogg';
	$Pubs[] = 'Savebutonu';
	$Pubs[] = 'Leadergamer';
	$Pubs[] = 'nserrano@ageteck.com.br';
	$Pubs[] = 'LINECORP';
	$Pubs[] = 'ClovenMedia';
	$Pubs[] = 'info@hypasos.com';
	$Pubs[] = 'theurbanroosters';
	$Pubs[] = 'Atedra';
	$Pubs[] = 'CodigopostalCL';
	$Pubs[] = 'Nestia';
	$Pubs[] = 'adcortoltda@gmail.com';
	$Pubs[] = 'EltiempoPe';
	$Pubs[] = 'techweekmag@gmail.com';
	$Pubs[] = 'Derecho-chile';
	$Pubs[] = 'Elhiwarettounsi';
	$Pubs[] = 'Mobezoom';
	$Pubs[] = 'Comiqueros';
	$Pubs[] = 'BlackVersions';
	$Pubs[] = 'Arabicprogrammer';
	$Pubs[] = 'Tabibby';
	$Pubs[] = 'info@friv5online.com';
	$Pubs[] = 'EdraPublishing';
	$Pubs[] = 'Ahmadhleihel';
	$Pubs[] = 'Flash-toons';
	$Pubs[] = 'AskFM';
	$Pubs[] = 'besoccer';
	$Pubs[] = 'Soccernet';
	$Pubs[] = 'Cucinaconme';
	$Pubs[] = 'Autobilis.Lt';
	$Pubs[] = 'bruno@net-horizons.com';
	$Pubs[] = 'Trendings';
	$Pubs[] = 'SeoWebbs';
	$Pubs[] = 'iLabRoma';
	$Pubs[] = 'RestodelCalcio';
	$Pubs[] = 'Lebombedivlad';
	$Pubs[] = 'Ebrave';
	$Pubs[] = 'NuvolaTV';
	$Pubs[] = 'bodybuilding-and-fitness';
	$Pubs[] = 'Gazzettagranata';
	$Pubs[] = 'AntenazadarHR';
	$Pubs[] = 'Storiedipremier';
	$Pubs[] = 'Minutidirecupero';
	$Pubs[] = 'Webinvent';
	$Pubs[] = 'Passionecalcio';
	$Pubs[] = 'RadioBanovinaHR';
	$Pubs[] = 'BrodportalHR';
	$Pubs[] = 'MeteoInfoHR';
	$Pubs[] = 'Weareblog';
	$Pubs[] = 'FantaPiu3';
	$Pubs[] = 'PersoneFamose';
	$Pubs[] = '1188';
	$Pubs[] = 'RootNation';
	$Pubs[] = 'ZagorjeInternationalHR';
	$Pubs[] = 'pypgamersnetwork';
	$Pubs[] = 'MetropolitanHR';
	
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
	