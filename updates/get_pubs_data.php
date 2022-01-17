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
	
	$Pubs[] = 'sohu';
	$Pubs[] = 'NONTRAD';
	$Pubs[] = 'noticiaszmg';
	$Pubs[] = 'Egool';
	$Pubs[] = 'Newsatual';
	$Pubs[] = 'evvelcevap';
	$Pubs[] = 'sportwitness';
	$Pubs[] = 'mumsnet';
	$Pubs[] = 'Queroviajarmais';
	$Pubs[] = 'CulredCo';
	$Pubs[] = 'sanjoseahora';
	$Pubs[] = 'CursoMecanet';
	$Pubs[] = 'NONTRAD2';
	$Pubs[] = 'MediosDigitalesMx';
	$Pubs[] = 'Vreme';
	$Pubs[] = 'ElDiario21Co';
	$Pubs[] = 'TheStandardMedia';
	$Pubs[] = 'Marcosilvanoticias';
	$Pubs[] = 'Aquinacozinha';
	$Pubs[] = 'Banhospoderosos';
	$Pubs[] = 'expertiza';
	$Pubs[] = 'Zurnal24';
	$Pubs[] = 'Pernambuconoticias';
	$Pubs[] = 'UTHRUSSIA';
	$Pubs[] = 'helloyishi';
	$Pubs[] = 'ExpresSK';
	$Pubs[] = 'LaSmorfiaNapoletanaIT';
	$Pubs[] = 'Madformadelskere';
	$Pubs[] = 'Evolutionadv.it';
	$Pubs[] = 'receptizadanas';
	$Pubs[] = 'PensioniPerTuttiIT';
	$Pubs[] = 'xxlmarketingsolution';
	$Pubs[] = 'TennisfeverIT';
	$Pubs[] = 'SanMarinoRTV';
	$Pubs[] = 'WestlandersNl';
	$Pubs[] = 'CalcioNapoli24IT';
	$Pubs[] = 'GrafillIT';
	$Pubs[] = 'Tandem';
	$Pubs[] = 'Nordest24IT';
	$Pubs[] = 'Osasuna1920';
	$Pubs[] = 'PhotoForum';
	$Pubs[] = 'WaterwegSportNl';
	$Pubs[] = 'JornalDoCentroPt';
	$Pubs[] = 'CliccandoNewsIT';
	
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
	