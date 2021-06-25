<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/common.lib.php');

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
	
	$sql = "SELECT * FROM finance_account";
	$query = $db2->query($sql);
	if($db2->num_rows($query) > 0){
		
		header('Content-Type: application/csv');
		header('Content-Disposition: attachment; filename=payments_'.date('Y-m-d-H-i-s').'.csv');
		header('Pragma: no-cache');
		
		echo '"ID Cuenta Financiera","Razon Social","Tipo de pago","Cuenta de Pago","Nombre del banco","SWIFT","IBAN","Moneda","Importe","Mes","Fecha de pago"' . "\n";
		
		while($Financial = $db2->fetch_array($query)){
			$idAcc = $Financial['id'];
			
			$AccountID = $Financial['accid'];
			$AccountName = $Financial['name'];
			
			//if(strlen($AccountID) <= 1){
			
				$sql = "SELECT * FROM payment WHERE finance_account_id = $idAcc AND year = 2020 AND status = 2 ORDER BY id ASC";
				$query2 = $db2->query($sql);
				if($db2->num_rows($query2) > 0){
					while($Payment = $db2->fetch_array($query2)){
						$idPay = $Payment['id'];
	
						$sql = "SELECT * FROM closure WHERE payment_id = $idPay ORDER BY id ASC";
						//$query3 = $db2->query($sql);
	
						//if($db2->num_rows($query3) > 0){
							//while($Close = $db2->fetch_array($query3)){
							//	$pubID = $Close['publisher_id'];
							/*	
								$sql = "SELECT user_id FROM publisher WHERE id = $pubID LIMIT 1";
								$user_id = $db2->getOne($sql);
								
								$sql = "SELECT username FROM user WHERE id = $user_id LIMIT 1";
								$Publisher = $db2->getOne($sql);
							*/	
								$AmountEUR = round($Payment['euramount'], 2);
								$AmountUSD = round($Payment['usdamount'], 2);
								
								if($Financial['currency_id'] == 2){
									$Curr = 'Euro';
									$Amount = $AmountEUR;
								}else{
									$Curr = 'Dolar';
									$Amount = $AmountUSD;
								}
								
								$Month = $Payment['month'] . '-' . $Payment['year'];
								//$arM = explode('-', $Close['finished_at']);
								//$Month = $arM[1] . '-' . $arM[0];
						
								if($Payment['status'] == 1){
									$Status = 'Pendiente Factura';
								}elseif($Payment['status'] == 2){
									$Status = 'Pendiente';
								}elseif($Payment['status'] == 3){
									$Status = 'Vencido';
								}elseif($Payment['status'] == 4){
									$Status = 'Acumulado';
								}elseif($Payment['status'] == 5){
									$Status = 'Pagado';
								}elseif($Payment['status'] == 6){
									$Status = 'Archivado';
								}else{
									$Status = '';
								}
								
								if($Financial['payment_type'] == 1){
									$Type = 'Transferencia';
									$Account = $Financial['payment_account'];
									$BankName = $Financial['bank_name'];
									$SWIFT = $Financial['swift'];
									$IBAN = $Financial['iban'];
								}else{
									$Type = 'Paypal';
									$Account = $Financial['paypal_account'];
									$BankName = '';
									$SWIFT = '';
									$IBAN = '';
								}
								
								$arEP = explode(' ',$Payment['estimated_payment_at']);
								$PD = $arEP[0];
								
								//ID Cuenta Financiera, Razon Social, Tipo de pago, Cuenta de Pago, Nombre del banco, SWIFT, IBAN, Moneda, Importe, Mes, Fecha de pago
								//\"$Publisher\", $AmountUSD\",\"$AmountEUR\"
								echo "\"$AccountID\",\"$AccountName\",\"$Type\",\"$Account\",\"$BankName\",\"$SWIFT\",\"$IBAN\",\"$Curr\",\"$Amount\",\"$Month\",\"$PD\" \n";
							//}
						//}
					}
				}
			//}
			//exit(0);
		}
	}
	
	
	
	/*
		Identificador de la cuenta
		Razon Social
		Tipo de pago
		Cuenta de Pago
		Nombre del banco
		SWIFT
		IBAN
		Moneda
		Importe neto
		Mes
		Fecha de pago estimada
	*/