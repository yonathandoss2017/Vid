<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('../config.php');
	require('../constantes.php');
	require('../db.php');
	require('../common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$ipAddress = $_SERVER['REMOTE_ADDR'];

function validateDate($date, $format = 'Y-m-d H:i:s'){
	$d = DateTime::createFromFormat($format, $date);
	return $d && $d->format($format) == $date;
}
	
	/*
	if(!isset($_POST['user'])){
		$_POST = $_GET;
	}
	*/
	
	$ValidUsers = array(3523,3181);
	
	if(isset($_POST['user'])){
		$user = my_clean($_POST['user']);
		if(strlen($_POST['password']) == 32){
			$pass = my_clean($_POST['password']);
		}else{
			$pass = md5($_POST['password']);
		}
				
		$sql = "SELECT id FROM " . USERS . " WHERE (user = '$user' OR email = '$user') AND password = '$pass' AND AccM != 15 AND AccM != 9999 LIMIT 1";
		$idPub = $db->getOne($sql);
		if($idPub > 0){
			if(in_array($idPub, $ValidUsers)){
				$Date = date('Y-m-d H:i:s');
				$sql = "INSERT INTO api_access (idUser, IP, Time) VALUES ('$idPub', '$ipAddress', '$Date')";
				$db->query($sql);
			}else{
				die('Login fail');
			}
		}else{
			die('Login fail');
		}

	}else{
		die('Login fail');
	}
	
	
	//$idPub = 3523;
	
	$sql = "SELECT currency, showi FROM " . USERS . " WHERE id = '$idPub'";
	$query = $db->query($sql);
	$userData = $db->fetch_array($query);
	$idCurrency = $userData['currency'];
	if($userData['showi'] == 1){
		$ShowCPM = false;
		$ShowPL = true;
		$ShowST = true;
	}elseif($userData['showi'] == 2){
		$ShowCPM = true;
		$ShowPL = true;
		$ShowST = true;
	}else{
		$ShowCPM = false;
		$ShowPL = false;
		$ShowST = false;
	}
	
	if($idCurrency == 2){
		$DEb = '€';
		$DE = '';
	}else{
		$DEb = '';
		$DE = '$';
	}

	$sql = "SELECT * FROM " . TAGS . " WHERE idUser = '$idPub'";
	$query = $db->query($sql);
	$TagList = array();
	while($Tag = $db->fetch_array($query)){
		$TagList[$Tag['id']]['RevenueType'] = $Tag['RevenueType'];
		$TagList[$Tag['id']]['Revenue'] = $Tag['Revenue'];
		$TagList[$Tag['id']]['PlatformType'] = $Tag['PlatformType'];
	}
	
	$Today = date('Y-m-d');
	if(isset($_POST['date'])){
		$Date = $_POST['date'];
		if(validateDate($Date, 'Y-m-d')){
			$FirstDay = $Date;
			$LastDay = $Date;
		}else{
			die('Error: Wrong date format');
		}
	}else{
		$FirstDay = $Today;
		$LastDay = $Today;
	}
	
	//$Yesterday = date('Y-m-d', time() - 86400);
	//$LastDay = date('Y-m-d',strtotime("-1 month"));
	
	
	
	header("Content-type: text/xml");
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo "\n<data>\n";
	echo "\n<date>" . $FirstDay . "</date>\n";
	echo "<sites>\n";
	
	$sql = "SELECT * FROM " . SITES . " WHERE idUser = '$idPub' AND deleted = 0 ORDER BY id DESC";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		
		while($Site = $db->fetch_array($query)){
			$idSite = $Site['id'];
		
			$sql = "SELECT idTag, SUM(Coste) AS Coste FROM " . STATS .  " WHERE idUser = '$idPub' AND idSite = '$idSite' AND Date BETWEEN '$FirstDay' AND '$LastDay' GROUP BY idTag";
			$query2 = $db->query($sql);
			//exit(0);
			$ThisRevenue = 0;
			$RevenueThis = 0;
			//$ThisImpressions = 0;
			if($db->num_rows($query2) > 0){
				while($dataTag = $db->fetch_array($query2)){
					$RevenueThis += $dataTag['Coste'];
					//$ThisImpressions += $dataTag['Impressions'];
				}
			}
			
			
			$ThisRevenue = correctCurrency($RevenueThis, $idCurrency);
			
			
			echo "	<site>\n";
			echo "		<sitename>" . $Site['sitename'] . "</sitename>\n";
			echo "		<revenue>" . $DE . number_format($ThisRevenue, 2, ',', '.') . $DEb . "</revenue>\n";
			echo "	</site>\n";
		}
		
	}
	echo "</sites>\n";
	echo "</data>\n";