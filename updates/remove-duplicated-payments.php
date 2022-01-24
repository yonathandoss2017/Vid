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
	

	$sql = "SELECT * FROM payment WHERE finance_account_id = 5356 ORDER BY year DESC, month DESC";
	$query = $db2->query($sql);
	if($db->num_rows($query) > 0){
		while($Pay = $db2->fetch_array($query)){
			$PaymentID = $Pay['id'];
			$Year = $Pay['year'];
			$Month = $Pay['month'];
			
			$sql = "SELECT COUNT(*) FROM closure WHERE payment_id = $PaymentID";
			$ClosureCount = $db2->getOne($sql);
			
			$sql = "SELECT COUNT(*) FROM invoice_payment WHERE payment_id = $PaymentID";
			$InvoiceCount = $db2->getOne($sql);
			
			if($ClosureCount > 0 || $InvoiceCount > 0){
				echo "$PaymentID - $Month/$Year Closures: $ClosureCount - Invoices: $InvoiceCount \n";
			}
		}
	}

	