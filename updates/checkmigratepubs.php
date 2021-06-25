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
	
	/*
	$dbuser2 = "root";
	$dbpass2 = "ViDo0-PROD_2020";
	$dbhost2 = "aa12gqfb9qs8z09.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	*/
	
	$dbuser2 = "root";
	$dbpass2 = "Jz8eDbamcNx3TskWzrjzH7g";
	$dbhost2 = "vidoomy-production.cpijmqdfbof9.eu-west-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	mysqli_set_charset($db2->link,'utf8');
	
	
	$sqlN = "INSERT INTO `notification` (`date`, `subject`, `message`, `link`, `priority`, `excerpt`, `tags`)
VALUES
	('2020-04-30 12:03:19', 'Bienvenido a tu nuevo panel!', '\n<div style=\"width: 100%; background-color: #FFFFFF;\">\n    <div style=\"margin: 0 auto;\" class=\"notification-container\">\n            <tr>\n        <td class=\"wrapper less-padding\" style=\"font-size: 12px; vertical-align: top; box-sizing: border-box;\">\n            <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"  style=\"border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;\">\n                <tr>\n                    <td>\n                    <p>{NAME}, Bienvenido al nuevo panel de Vidoomy. En este nuevo panel, además de un nuevo diseño, podrás ver más y mejor información acerca de tu cuenta, pagos y mucho más!</p><p><br></p><p>Esperemos que te guste!</p>\n                    </td>\n                </tr>\n            </table>\n        </td>\n    </tr>\n    <tr>\n        <td style=\"padding: 25px\"></td>\n    </tr>\n    </div>\n</div>\n', NULL, 2, 'En este nuevo panel, además de un nuevo diseño, podrás ver más y mejor información acerca de tu cuenta, pagos y mucho más!', '[\"important\"]')";
	
	//$db2->query($sql);
	$Pubs = array(
//		"ElmostradorCL", 				//OK
		"palcocomunicaciones",			//OK - Acumula
//		"grupolarepublica",				//OK
		"solowrestling",				//OK - Acumula
/*
		"canalcapital",					//OK
		"diariodeavisos",				//OK
		"elblog",						//OK - Acumula pero tiene pendientes de factura
		"eldeber",						//OK
*/
		"eldinamocl",					//OK - Acumula
/*
		"elobservador",					//OK
		"eventossantamarta",			//OK
		"grupocorprensa",				//OK
		"grupoeditorialnotmusa",		//OK		
		"gruporcnradio",				//OK
		"guatemala",					//OK				
		"huffingtonpost",				//OK
//		"jorel@solonoticias.com",		//OK
		"urgente24",					//OK
		"seguimientoco",				//OK
		"grupoturner",					//OK
		"deaglemedia",					//OK
		"diariolahuella",				//OK
		"historiaybiografia",			//OK
		"merca20",						//OK						
		"crhoy",						//OK
		"ecoosfera",					//OK
		"viraloagroup",					//OK
		"teleamazonas",					//OK
		"periodicocentral",				//OK
		"grupoextra",					//OK
		"radioagricultura",				//OK
		"aeoletv",						//OK
		"adxion",						//OK
		"grupocelsa",					//OK
		"exitosanoticias",				//OK
		"repretel",						//OK
		"cuarteldelmetal",				//OK
		"diariocambio",					//OK
		"elespanol",					//OK
		"copesa",						//OK
//		"digomedia",					//TODO PENDIENTE VENCIDO
//		"bluemedia",					//OK
		"infobaear",					//OK
		"gese",							//OK
		"lacapital",					//OK
		"culturizando",					//OK
		"everardoherrera",				//OK
		"cloudstudio",					//OK
		"minecrafteo",					//OK
		"wikiguate"						//OK
*/
	);
	
	$Pubs = array(
"juicebarads",
"mundopositivo",
"altoastral",
"CGN",
"ilustrado",
"diariosassociados",
"diariosassociadosbrasilia",
"Webediabrasil",
"grupoindependente",
"gruporbs",
"otempo",
"odia",
"GRPCOM",
"Ichacha",
"administradoresbr",
"theclinic",
"oscar.leung@networld.hk",
"DAZNbr",
"metro",
"gazetawebBR",
"revistabula",
"lancenet",
"superela",
"InnovaMedia",
"Pradyutchy@gmail.com",
"hojeemdia",
"tribunaonline",
"eurogamer",
"ibahia",
"Pilio",
"yieldpass",
"eurogamerespana",
"cartacapital",
"aldiario",
"mantosdofutebol",
"publy",
"webgo",
"varelanoticias",
"netflu",
"diariodepernambuco",
"Typingmaster",
"riovagas",
"dailycannon",
"novoextra",
"rioja2",
"miquelalcayne@gmail.com",
"correio24",
"palmeirasonline",
"grupoperiscopio",
"climatempo",
"eldiariobolivia",
"SPFC",
"viomundo",
"jenesaispop",
"percatalunya",
"gridmidia",
"elnuevodiario",
"flashback",
"mulhersemphotoshop",
"Larepublicacat",
"yeuthethao_vn",
"carlosrhc@sanateysana.com",
"info@mudet.com",
"GrupoNed",
"grupocooperativa",
"bayardrevistas",
"grupocronica",
"animalpolitico",
"nacion",
"hipertextual",
"latina",
"montevideo",
"ecuavisa",
"grupoespasa",
"eluniverso",
"iniciaseo@gmail.com",
"hotdogmedia",
"paginasiete",
"caretasperu",
"sentidodemujer",
"mientrastantomx"
	);
	
	$M = array(
		26317, 26743, 1390, 189, 1018
	);
	
	foreach($Pubs as $Pub){
		$sql = "SELECT id FROM users WHERE user LIKE '$Pub' LIMIT 1";
		$idPub = $db->getOne($sql);
		
		echo "ID: $idPub ";
		
		$Show = true;
		
		/*
		$sql = "SELECT lang FROM users WHERE user LIKE '$Pub' LIMIT 1";
		$Lang = $db->getOne($sql);
	
		$sql = "SELECT COUNT(*) FROM login_en WHERE idUser = $idPub";
		$Counts = $db->getOne($sql);
		if($Counts > 0){
			$Ing = 'SI ' . $Counts;
			$Show = true;
		}else{
			$Ing = 'NO';
			$Show = false;
		}
		
		
		if(in_array($idPub, $M)){
			$AO = 'a';
		}else{
			$AO = 'o';
		}
		*/
		//$sql = "SELECT name FROM user WHERE id = $idPub LIMIT 1";
		//$Name = $db2->getOne($sql);
		
		//$sqlP = str_replace('{NAME}', $Name, $sqlN);
		//$sqlP = str_replace('{AO}', $AO, $sqlP);
		//echo $sqlP;		
		//$db2->query($sqlP);
		//$NOTID = mysqli_insert_id($db2->link);
		//$NOTID = 54;
		
		//$sql2 = "INSERT INTO notifiable_entity (identifier, class) VALUES ($idPub, 'App\\Entity\\User')";
		//$db2->query($sql2);
		//$NE = mysqli_insert_id($db2->link);
		/*
		$sql = "SELECT id FROM notifiable_entity WHERE identifier = $idPub LIMIT 1";
		$NE = $db2->getOne($sql);
		
		$sql2 = "INSERT INTO notifiable_notification (notification_id, notifiable_entity_id, seen) VALUES ($NOTID, $NE, 0)";
		echo $sql2;
		$db2->query($sql2);
		*/
		//exit(0);
		
		

		$sql = "SELECT password FROM user WHERE id = $idPub LIMIT 1";
		$PassL = strlen($db2->getOne($sql));
		
		if($PassL == 32){
			$sql = "UPDATE user SET encoder = 'old' WHERE id = $idPub LIMIT 1";
			$db2->query($sql);
		}else{
			$sql = "UPDATE user SET encoder = 'new' WHERE id = $idPub LIMIT 1";
			$db2->query($sql);
		}
		
		$sql = "UPDATE users SET enable_new = 1 WHERE id = $idPub LIMIT 1";
		$db->query($sql);

			
		$sql = "SELECT finance_account_id FROM publisher WHERE user_id = '$idPub'";
		$idFin = $db2->getOne($sql);
		
		$sql = "SELECT currency_id FROM finance_account WHERE id = $idFin LIMIT 1";
		if($db2->getOne($sql) == 2){
			$Curr = 'EUR';
		}else{
			$Curr = 'USD';
		}
		if($Show){
			//echo "Username: $Pub - Pub ID: $idPub -    $PassL   \n ";
			echo $Pub . "\n ";
		}
	}