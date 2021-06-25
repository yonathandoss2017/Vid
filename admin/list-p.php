<?php
	session_start();
	define('CONST',1);
	require('../config.php');
	require('../db.php');
	require('../constantes.php');
	require('../common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	header("Content-type: text/csv; charset=utf-8");
	header("Content-Disposition: attachment; filename=file.csv");
	header("Pragma: no-cache");
	header("Expires: 0");

	exit(0);
	echo 'Usuario,Nick,Nombre,Email,Moneda,Estado Fiscal,NIF/CIF,Empresa,Pais,Provincia,Ciudad,CP,Direccion,Forma de Pago,Cuenta,Banco,PaisBanco,SWIFT,IBAN' . "\n";
	//exit();
	$sql = "SELECT * FROM " . USERS . " WHERE deleted = 0 AND type = 0 AND  AccM != 15 AND AccM != 9999 ORDER BY user ASC";
	
	
	$N = 0;
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($User = $db->fetch_array($query)){
			echo $User['user'] . ",";
			echo $User['nick'] . ",";
			echo '"' . utf8_decode($User['name']) . " " . utf8_encode($User['lastname']) . '",';
			echo $User['email'] . ",";
			if($User['currency']==1){
				echo 'USD,';
			}else{
				echo 'EUR,';
			}
			if($User['ef']==1){
				echo 'Empresa,';
			}else{
				echo 'Autonomo,';
			}
			echo '"' . $User['nifcif'] . '",';
			echo '"' . $User['company'] . '",';
			$idCountry = $User['country'];
			if($idCountry > 0){
				$sql = "SELECT country_name FROM countries WHERE id = $idCountry LIMIT 1";
				echo $db->getOne($sql) . ",";
			}else{
				echo ",";
			}
			echo trim($User['province']) . ",";
			echo $User['city'] . ",";
			echo $User['cp'] . ",";
			echo '"' . $User['address'] . '",';
			if($User['paymenttype']==1){
				echo 'Paypal,';
			}else{
				echo 'Transferencia,';
			}
			echo '"' . $User['account'] . '",';
			echo '"' . $User['bankname'] . '",';
			$idBCountry = $User['bankcountry'];
			if($idBCountry > 0){
				$sql = "SELECT country_name FROM countries WHERE id = $idBCountry LIMIT 1";
				echo $db->getOne($sql) . ",";
			}else{
				echo ",";
			}
			echo '"' . $User['swift'] . '",';
			echo '"' . $User['iban'] . '"';
			echo "\n";
			$N++;
			if($N >= 130){
				//exit(0);
			}
		}
	}