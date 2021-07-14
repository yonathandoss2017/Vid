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
	
	$Pubs[] = 'MediaGeuzenBe';
	$Pubs[] = 'mariskalrock';
	$Pubs[] = 'Medyafaresi';
	$Pubs[] = 'TalksMedia';
	$Pubs[] = 'menshouse';
	$Pubs[] = 'Publieco';
	$Pubs[] = 'EspalhafactosPt';
	$Pubs[] = 'RaposafixePt';
	$Pubs[] = 'Kohokohta';
	$Pubs[] = 'Geosnews';
	$Pubs[] = 'pleine lune';
	$Pubs[] = 'ABCOnlineMedia';
	$Pubs[] = 'FluiddoGrupoPt';
	$Pubs[] = 'kurzy1';
	$Pubs[] = 'Improvemedia';
	$Pubs[] = 'GamingPl';
	$Pubs[] = 'NraLv';
	$Pubs[] = 'parislove';
	$Pubs[] = 'pnewsbe';
	$Pubs[] = 'cartes de france';
	$Pubs[] = 'InstaUserName';
	$Pubs[] = 'otzasada';
	$Pubs[] = 'southernstar';
	$Pubs[] = 'AutosportComRu';
	$Pubs[] = 'collegetimes';
	$Pubs[] = 'Targovishtebg';
	$Pubs[] = 'Autobild';
	$Pubs[] = 'e-burgas';
	$Pubs[] = 'WotPack';
	$Pubs[] = 'Hapche';
	$Pubs[] = 'KinoNavigator';
	$Pubs[] = 'SibenikMeteo';
	$Pubs[] = '1scandal';
	$Pubs[] = 'emprendedores';
	$Pubs[] = 'CarRu';
	$Pubs[] = 'Optima';
	$Pubs[] = 'revistaglamour';
	$Pubs[] = 'cbnglobo';
	$Pubs[] = 'Animekage';
	$Pubs[] = 'gaveanews';
	$Pubs[] = 'gazeteduvar';
	$Pubs[] = 'murat@dline.com.tr';
	$Pubs[] = 'infomercado.pe';
	$Pubs[] = 'Echoroukonline';
	$Pubs[] = 'novelasligeras';
	$Pubs[] = 'bhfmglobo';
	$Pubs[] = 'lanacionco';
	$Pubs[] = 'koreaherald';
	$Pubs[] = 'BusinessnewsTn';
	$Pubs[] = 'kzoltan@cylex.ro';
	$Pubs[] = 'Xproxxx';
	$Pubs[] = 'mcodina@tangodigitalagency.com';
	$Pubs[] = 'Bolgegundem';
	$Pubs[] = 'Radiomunera';
	$Pubs[] = 'info@coolstreaming.us';
	$Pubs[] = 'galwaybay';
	$Pubs[] = 'Dimsumdaily';
	$Pubs[] = 'HiveDigitalMedia';
	$Pubs[] = 'Mediotiempo';
	$Pubs[] = 'davidperez@webhoy.es';
	$Pubs[] = 'larepublicaec';
	$Pubs[] = 'Aofsoru';
	$Pubs[] = 'info@webvigo.com';
	$Pubs[] = 'atividadenews';
	$Pubs[] = 'ctgoodjobs';
	$Pubs[] = 'radioglobo';
	$Pubs[] = 'Arabiaweather';
	$Pubs[] = 'Grupo GPR';
	$Pubs[] = 'jeremy.benhaim.91@gmail.com';
	$Pubs[] = 'lucamontresor@gmail.com';
	$Pubs[] = 'contact@actupenit.com';
	$Pubs[] = 'VerdadeiroolharPt';
	$Pubs[] = 'Elbashayer';
	$Pubs[] = 'netxee';
	$Pubs[] = 'grupocapitaldigital';
	$Pubs[] = 'Viralshoc';
	$Pubs[] = '3almalt9nia';
	$Pubs[] = 'Direkto.rs';
	$Pubs[] = 'cloudkayaklabs';
	$Pubs[] = 'fightpasssite@gmail.com';
	$Pubs[] = 'jwad@rankone.live';
	$Pubs[] = 'Cimeco';
	$Pubs[] = 'noticierocontable.com';
	$Pubs[] = 'UNO';
	$Pubs[] = 'ecuagol_adops';
	$Pubs[] = 'VoltAfricaZa';
	$Pubs[] = 'Trendmedia';	
	
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
	