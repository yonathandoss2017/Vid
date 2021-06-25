<?php	
	//exit();
	
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
	require '/var/www/html/site/include/PHPMailer/PHPMailerAutoload.php';
	require('/var/www/html/login/admin/lkqdimport/common.php');
	
	//exit(0);
	
	$cookie_file = '/var/www/html/login/admin/lkqdimport/cookie.txt';
	
	$Date = date('Y-m-d', time() - 1200);
	//$Date = '2020-09-14';
	$DateToday = date('Y-m-d', time());
	
	if($Date != $DateToday){
		$LastU = 'Last Update';
	}else{
		$LastU = '';
	}
	
	$DateFrom = $Date;
	$DateTo = $Date;
	

	$HFrom = date('G', time() - 1200);
	$HTo = date('G', time() - 1200);
	$HFrom = 0;
	//$HTo = 23;
	//sleep(rand(1,90));
	
	//echo "Import $DateFrom to $DateTo - $HFrom $HTo \n";
	//exit(0);
	$StartTime = round(microtime(true) * 1000);
	echo 'Start: ' . $StartTime . "\n";
	
	
	$ImportData = getHourDataCSV($DateFrom, $DateTo, $HFrom, $HTo);
	
	if($ImportData === false){
		echo "Loggin in... \n\n";
		logIn();
		$ImportData = getHourDataCSV($DateFrom, $DateTo, $HFrom, $HTo);
	}
	
	echo 'Import Finish: ' . (round(microtime(true) * 1000) - $StartTime) . " COUNT: " . count($ImportData) . "\n";
	//print_r($ImportData);
	//exit(0);
	
	$N = 0;
	$Nn = 0;
	
	if($ImportData !== false){
		
		foreach($ImportData as $DataK => $DataL){
			$Nn = 0;
			$Bucle1 = 'Si';

			foreach($DataL as $Line){
				$Bucle2 = 'Si';
				echo $DataK . ": " . $Line . ' _ ' . $N . ' - ' . $Nn . "\n";
				if($N > 0){
					if($Nn == 0){
						if(strpos($Line, 'T') !== false  ){
							$arTime = explode("T", $Line);
							$Hour = $arTime[1];
							$Date = $arTime[0];
						}else{
							$Last = true;
							break;
						}
					}
					if($Nn == 1){ $LKQDuser = $Line; }
					if($Nn == 2){ $TagId = $Line; }
					if($Nn == 4){ $Domain = $Line; }
					if($Nn == 5){ $Country = $Line; }
					if($Nn == 6){ $Opportunities = takeComa($Line); }
					if($Nn == 7){ $Impressions = takeComa($Line); }
					if($Nn == 8){ $CPM = takeMoney($Line); }
					if($Nn == 9){ $Revenue = takeMoney($Line); }
					if($Nn == 10){ $Coste = takeMoney($Line); }
					if($Nn == 11){ $formatLoads = takeComa($Line); }
					if($Nn == 12){ $Clicks = takeComa($Line); }
					if($Nn == 13){ $FirstQuartiles = takeComa($Line); }
					if($Nn == 14){ $Midpoints = takeComa($Line); }
					if($Nn == 15){ $ThirdQuartiles = takeComa($Line); }
					if($Nn == 16){ $CompletedViews = takeComa($Line); }
					if($Nn == 17){ $adStarts = takeComa($Line); }
					$Wins = 0;
				}
				$Nn++;
				$N++;
			}
			
			//echo strlen($Line) . '=' . $Date . "\n";
			
		}
	}