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
	$db3 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	$idAcc = 69;//4959;//19568
	
	//$sql = "SELECT COUNT(*) FROM publisher WHERE finance_account_id = $idAcc";
	//$CNT = intval($db2->getOne($sql));

		$sql = "SELECT * FROM publisher WHERE finance_account_id = $idAcc";
		$query2 = $db3->query($sql);
		if($db3->num_rows($query2) > 0){
			while($Pub = $db3->fetch_array($query2)){
				$UsersIds[$Pub['user_id']] = $Pub['id'];
			}
		}
	
	$From = 11;
	$To = 12;

	$USDT = 0;
	$EURT = 0;
	$PatS = '';
	
	for($F=$From; $F <= $To; $F++){		
		$date1 = new DateTime("2019-$F-01");
		$DTo = $date1->format('Y-m-t');
		$DFrom = $date1->format('Y-m-01');
		$created_at = date("Y-m-d H:i:s");
		
		$USDT = 0;
		$EURT = 0;
		
		foreach($UsersIds as $UserId => $PubId){
			$sql = "SELECT SUM(Coste) AS USD, SUM(CosteEur) AS EUR FROM stats WHERE idUser = $UserId AND Date BETWEEN '$DFrom' AND '$DTo'";
			//echo $sql . "\n";
			$query = $db->query($sql);
			$Amount = $db3->fetch_array($query);
	
			$USD = $Amount['USD'];
			$EUR = $Amount['EUR'];
			
			if($USD > 0.01){
				
				$sql = "INSERT INTO vidoomy.closure (`publisher_id`, `started_at`, `finished_at`, `usdamount`, `created_at`, `created_by`, `euramount`)
	VALUES
	($PubId, '$DFrom 00:00:00', '$DTo 23:59:59', '$USD', '$created_at', 1, $EUR);";
				
				echo $sql . "\n";
				
				$USDT += $USD;
				$EURT += $EUR;
			}
		}
		
		$sql = "INSERT INTO `vidoomy`.`payment` (`currency_id`, `status`, `usdamount`, `created_at`, `created_by`, `year`, `month`, `estimated_payment_at`, `euramount`, `finance_account_id`) VALUES ('1', '5', '$USDT', 'TIMESTAMP', '1', '2019', '$F', '', '$EURT', '$idAcc');";
				
		$PatS .= $sql . "\n";
		
	}
	
	echo $PatS;
	
	//echo "Total USD: $USDT \n";
	//echo "Total EUR: $EURT \n";
	
	exit(0);
	$date1 = new DateTime("2019-$From-01");
	$DDFrom = $date1->format('Y-m-01');
	
	$date2 = new DateTime("2019-$To-01");
	$DDTo = $date2->format('Y-m-t');
	$DDFrom = '2019-12-01';
	$DDTo = '2020-04-30';
	
	$sql = "SELECT SUM(Coste) AS USD, SUM(CosteEur) AS EUR FROM stats WHERE idUser = $UserId AND Date BETWEEN '$DDFrom' AND '$DDTo'";
	$query = $db->query($sql);
	$TAmount = $db3->fetch_array($query);
	
	print_r($TAmount);