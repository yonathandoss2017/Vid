<?php
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/reports_/adv/config.php');
	//require('/var/www/html/login/reports_/adv/config_pre.php');
	require('/var/www/html/login/db.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	
	
	$sql = "SELECT * FROM `reports_domain_names` WHERE Name IN('obozrevatel.com','napravisam.bg','enciclopediaeconomica.com','novaconca.cat','dhnet.be','highmotor.com','crescebene.com','euskadinoticias.es','elmon.cat','instausername.com','kapital-rus.ru','lacalleochotv.org','ratkojat.fi','tarifs.org','werbestats.de','vadegust.cat','pleine-lune.org','vlaanderen-fietsland.be','mobilissimo.ro','nyheder24.dk','pildorasdefe.net','autosport.com.ru','politirapporten.dk','sportvsonlinetv.com','mrpiracy.top','stream2watch.sx','hesgoal.tv','dagens.no','fodboldnyheder.dk','senest.dk','sportzonline.to','culturaencadena.com','mammeoggi.it','revistascratch.com','monplaneta.cat','numericalcio.it','monterrassa.cat','totbarcelona.cat')";
	$Coma = '';
	$idCountryList = '';
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Country = $db->fetch_array($query)){
			
			$idCountryList .= $Coma . $Country['id'];
			$Coma = ',';
			
		}
	}
	
	echo $idCountryList;