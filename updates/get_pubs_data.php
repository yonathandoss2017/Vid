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
	
	$Pubs[] = 'fupa.net';
	$Pubs[] = 'perform';
	$Pubs[] = 'ProjectADV';
	$Pubs[] = 'NodeMapp';
	$Pubs[] = 'BDUMedia';
	$Pubs[] = 'Westlanders';
	$Pubs[] = 'boosters';
	$Pubs[] = 'RegiaoSulPt';
	$Pubs[] = 'distilled';
	$Pubs[] = 'DiligentPixelPt';
	$Pubs[] = 'Yobee';
	$Pubs[] = 'Vechtsportinfo';
	$Pubs[] = 'ComunicateES';
	$Pubs[] = 'GoToPortugal';
	$Pubs[] = 'Minbaad';
	$Pubs[] = 'Automobili';
	$Pubs[] = 'SDMGroup';
	$Pubs[] = 'ShowGamerCom';
	$Pubs[] = 'FightNews';
	$Pubs[] = 'AgoraPL';
	$Pubs[] = 'Moovy';
	$Pubs[] = 'KapitalRus';
	$Pubs[] = 'Primostenplus';
	$Pubs[] = 'Likaclub';
	$Pubs[] = 'Ljepotaizdravlje';
	$Pubs[] = 'NPMedia';
	$Pubs[] = 'Bswireless';
	$Pubs[] = 'Jatrgovac';
	$Pubs[] = 'Sesvete-danas';
	$Pubs[] = 'AutoKartaHrvatske';
	$Pubs[] = 'Svijetkulture';
	$Pubs[] = 'Plitvicetimes';
	$Pubs[] = 'SvoeDeloPlus';
	$Pubs[] = 'Modnemarke';
	$Pubs[] = 'Semana';
	$Pubs[] = 'sergio.anru@gmail.com';
	$Pubs[] = 'asdrubalsuarez.2020@gmail.com';
	$Pubs[] = 'unoargentina';
	$Pubs[] = 'Drbna';
	$Pubs[] = 'etnet';
	$Pubs[] = 'ComunicaEP';
	$Pubs[] = 'antena1';
	$Pubs[] = 'Jovenesweb';
	$Pubs[] = 'DearSAfrica';
	$Pubs[] = 'Property360';
	$Pubs[] = 'info@coolstreaming.us';
	$Pubs[] = '970universal';
	$Pubs[] = 'lagazettedufennec';
	$Pubs[] = 'Argaam21';
	$Pubs[] = 'kzoltan@cylex.ro';
	$Pubs[] = 'Ubica';
	$Pubs[] = 'Rqeeqa';
	$Pubs[] = 'medi1tv';
	$Pubs[] = 'PicoyPlaca';
	$Pubs[] = 'sba7egypt';
	$Pubs[] = 'PrimiciasEC';
	$Pubs[] = 'Mrmero';
	$Pubs[] = 'nzn';
	$Pubs[] = 'Hardwaretimes';
	$Pubs[] = 'Gastronomiabolivia';
	$Pubs[] = 'boatos';
	$Pubs[] = 'techferramentas';
	$Pubs[] = 'bobaedream';
	$Pubs[] = 'holidaypint';
	$Pubs[] = 'eldiarioecuador';
	$Pubs[] = 'Idinheiro';
	$Pubs[] = 'AltaIntensidad';
	$Pubs[] = 'DeNovelas';
	$Pubs[] = 'motociclismoonline';
	$Pubs[] = 'DiarioElSol';
	$Pubs[] = 'jamaicanmedium';
	$Pubs[] = 'belbalady';
	$Pubs[] = 'politicaaovivo';
	$Pubs[] = 'Autozine';
	$Pubs[] = 'Latinus';
	$Pubs[] = 'Gulf-up';
	$Pubs[] = 'SaboraVidaBr';
	$Pubs[] = 'lucamontresor@gmail.com';
	$Pubs[] = 'Annajah';
	$Pubs[] = 'dz-phones';
	$Pubs[] = 'tititudorancea';
	$Pubs[] = 'thenewsmy';
	$Pubs[] = 'Kino';
	
	
	
	
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
	