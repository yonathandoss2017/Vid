 <?php	
	//@session_start();
	// Guardamos cualquier error //
	//ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	/*if($_SESSION['Admin']!=1){
		header('Location: /');
		exit(0);
	}*/
	require('../config.php');
	require('../constantes.php');
	require('../db.php');
	//require('common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	require_once 'simplexlsx.class.php';
	
	$n = 0;
	//$xlsx = new SimpleXLSX('xls.xlsx');
	$xlsx = new SimpleXLSX('data_1.xlsx');
	foreach( $xlsx->rows(1) as $row){
		$n++;
		if($n > 7){
			$IFA = $row[6];
			$IP = $row[7];
			$UA = $row[8];
			$LAT = $row[9];
			$LONG = $row[10]; 
			
			$sql = "INSERT INTO 2xls (IFA, IP, UA, Latitude, Longitude) VALUES ('$IFA','$IP','$UA','$LAT','$LONG')";
			//echo '<br/>';
			$db->query($sql);
			//echo mysqli_errno($db->link) . ": " . mysqli_error($db->link) . "\n";

			
			//exit(0);
			
		}
		//print_r($row);
		
		//if($n >= 529418){
		if($n >= 8){
			//exit(0);
		}
	}
	
	echo 'OK';
	
	
	
?>