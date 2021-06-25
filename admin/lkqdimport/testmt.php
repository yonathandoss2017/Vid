<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 1);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/common.lib.php');
	require('/var/www/html/login/admin/lkqdimport/common.php');
	require '/var/www/html/site/include/PHPMailer/PHPMailerAutoload.php';
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	//echo date("H:i:s\n", time());
	
	
	$cookie_file = '/var/www/html/login/admin/lkqdimport/cookie.txt';

	$Date = date('Y-m-d', time() );
	$Hour = date('G', time() );
	//$Hour = date('G', time());
	//$Hour = $Hour - 1;
	$HFrom = date('G', time() - 8000);
	//$HFrom = $HFrom - 1;
	echo $Date . ': ' . $HFrom . ' - ' . $Hour . "\n";


	if($Hour <= 2){
		$sql = "TRUNCATE demandreport";
		$db->query($sql);
	}
	
	exit(0);
	//Import new data from Demand Reports
	
	//sleep(rand(1,300));

	$ImportData = getDateDemandReportCSV($Date, $HFrom, $Hour);
	
	if($ImportData === false){
		echo "Loggin in... \n\n";
		logIn();
		$ImportData = getDateDemandReportCSV($Date, $HFrom, $Hour);
	}
	
	print_r($ImportData);