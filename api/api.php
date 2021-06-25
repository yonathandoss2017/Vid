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
	$db2 = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$dbuser2 = "root";
	$dbpass2 = "Jz8eDbamcNx3TskWzrjzH7g";
	$dbhost2 = "vidoomy-production.cpijmqdfbof9.eu-west-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db3 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	$ipAddress = $_SERVER['REMOTE_ADDR'];

	$Devices[1] = 'desktop';
	$Devices[2] = 'mobile';

function validateDate($date, $format = 'Y-m-d H:i:s'){
	$d = DateTime::createFromFormat($format, $date);
	return $d && $d->format($format) == $date;
}
	header("Content-type: text/xml");
	
	if(isset($_POST['user'])){
		$user = my_clean($_POST['user']);
		if(strlen($_POST['password']) >= 32){// || strlen($_POST['password']) == 60
			$pass = my_clean($_POST['password']);
		}else{
			$pass = md5($_POST['password']);
			$NotEPass = my_clean($_POST['password']);
		}
		
		
		
		$sql = "SELECT " . USERS . ".id FROM " . USERS . " 
		INNER JOIN api_allowed ON " . USERS . ".id = api_allowed.idUser 
		WHERE (" . USERS . ".user = '$user' OR " . USERS . ".email = '$user') AND " . USERS . ".password = '$pass' AND " . USERS . ".AccM != 15 AND " . USERS . ".AccM != 9999 LIMIT 1";
		$idPub = $db->getOne($sql);
		
	
		
		if($idPub > 0){
			$Date = date('Y-m-d H:i:s');
			$sql = "INSERT INTO api_access (idUser, IP, Time, Type) VALUES ('$idPub', '$ipAddress', '$Date', 2)";
			$db->query($sql);
		}else{
			$sql = "SELECT " . USERS . ".password FROM " . USERS . " 
			INNER JOIN api_allowed ON " . USERS . ".id = api_allowed.idUser 
			WHERE (" . USERS . ".user = '$user' OR " . USERS . ".email = '$user') AND " . USERS . ".AccM != 15 AND " . USERS . ".AccM != 9999 LIMIT 1";
			$CPass = $db->getOne($sql);
			//echo "<!-- $CPass - $NotEPass -->";
			if(password_verify($NotEPass, $CPass)){
				$sql = "SELECT id FROM " . USERS . " WHERE (user = '$user' OR email = '$user') AND AccM != 15 AND AccM != 9999 LIMIT 1";
				$idPub = $db->getOne($sql);
				
				$Date = date('Y-m-d H:i:s');
				$sql = "INSERT INTO api_access (idUser, IP, Time, Type) VALUES ('$idPub', '$ipAddress', '$Date', 2)";
				$db->query($sql);
			
			}else{
				die('Login fail2');
			}
		}
	}else{
		die('Login faill');
	}
	
	$sql = "SELECT * FROM api_allowed WHERE idUser = '$idPub'";
	$query = $db->query($sql);
	$ApiData = $db->fetch_array($query);
	
	$sql = "SELECT f.currency_id AS currency FROM finance_account f
	INNER JOIN publishers p ON p.finance_account_id = f.id 
	INNER JOIN user u ON p.user_id = u.id
	WHERE u.id = $idPub";
	
	$sql = "SELECT currency FROM " . USERS . " WHERE id = '$idPub'";
	$idCurrency = $db->getOne($sql);
	
	if($idCurrency == 2){
		$DEb = '€';
		$DE = '';
		$CurrName = 'EUR';
		$Field = 'CosteEur';
	}else{
		$DEb = '';
		$DE = '$';
		$CurrName = 'USD';
		$Field = 'Coste';
	}

	$sql = "SELECT * FROM " . TAGS . " WHERE idUser = '$idPub'";
	$query = $db->query($sql);
	$TagList = array();
	while($Tag = $db->fetch_array($query)){
		$TagList[$Tag['id']]['RevenueType'] = $Tag['RevenueType'];
		$TagList[$Tag['id']]['Revenue'] = $Tag['Revenue'];
		$TagList[$Tag['id']]['PlatformType'] = $Tag['PlatformType'];
	}
		
	if(isset($_POST['date_from']) && isset($_POST['date_to'])){
		$Date1 = $_POST['date_from'];
		$Date2 = $_POST['date_to'];
		if(validateDate($Date1, 'Y-m-d') && validateDate($Date2, 'Y-m-d')){
			$FirstDay = $Date1;
			$LastDay = $Date2;
			
			$DateTime1 = new DateTime($Date1);
			$DateTime2 = new DateTime($Date2);
			
			$Interval = $DateTime1->diff($DateTime2);
			if($Interval->format('%a') > 60){
				die('Error: 60 days limit exceeded');
			}
			
			if($DateTime1 > $DateTime2){
				die('Error: date_from can not be after date_to');
			}
			
			$Today = new DateTime('NOW');
			
			if($DateTime2 > $Today){
				die('Error: dates can not be after today');
			}
		}else{
			die('Error: Wrong date format');
		}
	}else{
		die('Error: Missing date');
	}
	
	if($ApiData['Imp'] == 1){
		$sql = "SELECT idTag, idSite, SUM($Field) as Rev, Date FROM `stats` WHERE `idUser` = $idPub AND `Date` BETWEEN '$FirstDay' AND '$LastDay' GROUP BY idTag, Date ORDER BY `id` DESC";
		$query = $db->query($sql);
		if($db->num_rows($query) > 0){
			while($S = $db->fetch_array($query)){
				$SRev = $S['Rev'];
				if($SRev > 0){
					$idTag = $S['idTag'];
					$idSite = $S['idSite'];
					$Date = $S['Date'];
					$CPM = rand($ApiData['Range1'] * 100, $ApiData['Range2'] * 100) / 100;
					//echo $CPM . "\n";
	
					$sql = "SELECT id FROM api_helper WHERE idTag = '$idTag' AND Date = '$Date'";
					$idH = $db->getOne($sql);
	
					if($idH > 0){
						$sql = "SELECT Rev FROM api_helper WHERE id = '$idH'";
						$HRev = $db->getOne($sql);
						if($HRev < $SRev){
							$Dif = $SRev - $HRev;
							
							$sql = "SELECT C FROM api_helper WHERE id = '$idH'";
							$NewC = $db->getOne($sql);

							$Imp = round($Dif / $NewC * 1000, 0, PHP_ROUND_HALF_EVEN);
							$sql = "UPDATE api_helper SET Rev = '$SRev', Imp = Imp + $Imp WHERE id = '$idH' LIMIT 1";
							$db->query($sql);
						}
					}else{
						$Imp = round($SRev / $CPM * 1000, 0, PHP_ROUND_HALF_EVEN);
						$Time = time();
						$sql = "INSERT INTO api_helper (idUser, idTag, Date, Imp, Rev, C, Time) VALUES ('$idPub', '$idTag', '$Date', '$Imp', '$SRev', '$CPM', '$Time')";
						$db->query($sql);
					}
				}
			}
		}
	}
	
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo "\n<data>";
	echo "\n  <startdate>" . $DateTime1->format('Y-m-d') . "</startdate>";
	echo "\n  <enddate>" . $DateTime2->format('Y-m-d') . "</enddate>";
	
	
	if(isset($_GET['by'])){
		if($_GET['by'] == 'date'){
			if(isset($_GET['by2'])){
				if($_GET['by2'] == 'site'){
					$Interval = $DateTime1->diff($DateTime2);
					echo "\n  <dates>";
					for($D = 0; $D <= $Interval->format('%a'); $D++){
						echo "\n    <entry>";
						echo "\n      <date>" . $DateTime1->format('Y-m-d') ."</date>";
							
						$sql2 = "SELECT * FROM sites WHERE idUser = '$idPub' AND deleted = 0 AND eric != 3 AND aproved = 0";
						$query2 = $db->query($sql2);
						if($db->num_rows($query2) > 0){
							echo "\n      <sites>";
							while($Site = $db->fetch_array($query2)){
								
								$idSite = $Site['id'];
								$sql = "SELECT SUM($Field) AS Rev FROM " . STATS .  " WHERE idUser = '$idPub' AND idSite = '$idSite' AND Date = '" . $DateTime1->format('Y-m-d') . "'";
								$Rev = $db2->getOne($sql);
								
								if(round($Rev, 2, PHP_ROUND_HALF_DOWN) >= 0.01){
								
									echo "\n        <entry>";
									echo "\n          <site>" . $Site['sitename'] ."</site>";
									echo "\n          <revenue currency=\"$CurrName\">" . number_format($Rev, 2, '.', '') . "</revenue>";
									
									if($ApiData['Imp'] == 1){
										$sql = "SELECT SUM(api_helper.Imp) AS Imp, (SUM(C) / COUNT(C)) AS Cp FROM api_helper 
										INNER JOIN supplytag ON supplytag.id = api_helper.idTag 
										WHERE api_helper.idUser = '$idPub' AND supplytag.idSite = '$idSite' AND api_helper.Date BETWEEN '" . $DateTime1->format('Y-m-d') . "' AND '" . $DateTime2->format('Y-m-d') . "'";
										//$Imp = intval($db2->getOne($sql));
										$q = $db2->query($sql);
										$sData = $db->fetch_array($q);
										
										$CPM = $sData['Cp'];
										$Imp = $sData['Imp'];
										
										echo "\n          <impressions>" . $Imp ."</impressions>";
										echo "\n          <ecpm currency=\"$CurrName\">" . number_format($CPM, 2, '.', '') ."</ecpm>";
									}
									
									echo "\n        </entry>";
								
								}
							}
							echo "\n      </sites>";
						}	
							
						echo "\n    </entry>";

						$DateTime1->modify('+1 day');
					}
					
					echo "\n  </dates>";
				}elseif($_GET['by2'] == 'device'){
					
					$Interval = $DateTime1->diff($DateTime2);
					echo "\n  <dates>";
					
					for($D = 0; $D <= $Interval->format('%a'); $D++){
						echo "\n    <entry>";
						echo "\n      <date>" . $DateTime1->format('Y-m-d') ."</date>";
						
						echo "\n      <devices>";
				
						foreach($Devices as $idDev => $NameDev){
							$sql = "SELECT SUM($Field) AS Rev FROM " . STATS .  " 
							INNER JOIN supplytag ON supplytag.id = " . STATS .  ".idTag 
							WHERE " . STATS .  ".idUser = '$idPub' AND supplytag.PlatformType = '$idDev' AND " . STATS .  ".Date = '" . $DateTime1->format('Y-m-d') . "'";
							$Rev = $db->getOne($sql);
							
							if(round($Rev, 2, PHP_ROUND_HALF_DOWN) >= 0.01){
								echo "\n        <entry>";
								echo "\n          <device>" . $NameDev ."</device>";
								echo "\n          <revenue currency=\"$CurrName\">" . number_format($Rev, 2, '.', '') . "</revenue>";
								
								if($ApiData['Imp'] == 1){
									$sql = "SELECT SUM(Imp) AS Imp, (SUM(C) / COUNT(C)) AS Cp FROM api_helper 
									INNER JOIN supplytag ON supplytag.id = api_helper.idTag 
									WHERE api_helper.idUser = '$idPub' AND supplytag.PlatformType = '$idDev' AND api_helper.Date = '" . $DateTime1->format('Y-m-d') . "'";
									//$Imp = $db->getOne($sql);
									//$CPM = $Rev / $Imp * 1000;
									$q = $db2->query($sql);
									$sData = $db->fetch_array($q);
									
									$CPM = $sData['Cp'];
									$Imp = $sData['Imp'];
									
									echo "\n          <impressions>" . $Imp ."</impressions>";
									echo "\n          <ecpm currency=\"$CurrName\">" . number_format($CPM, 2, '.', '') ."</ecpm>";
								}
								echo "\n        </entry>";
							}
						}
						
						echo "\n      </devices>";
						
						$DateTime1->modify('+1 day');
					}
					echo "\n  </dates>";          
				}
			}else{
				$Interval = $DateTime1->diff($DateTime2);
				echo "\n  <dates>";
				for($D = 0; $D <= $Interval->format('%a'); $D++){
					$sql = "SELECT SUM($Field) AS Rev FROM " . STATS .  " WHERE idUser = '$idPub' AND Date = '" . $DateTime1->format('Y-m-d') . "'";
					$Rev = $db->getOne($sql);
					
					if(round($Rev, 2, PHP_ROUND_HALF_DOWN) >= 0.01){
					
						echo "\n    <entry>";
						echo "\n      <date>" . $DateTime1->format('Y-m-d') ."</date>";
						echo "\n      <revenue currency=\"$CurrName\">" . number_format($Rev, 2, '.', '') . "</revenue>";
						
						if($ApiData['Imp'] == 1){
							$sql = "SELECT SUM(Imp) AS Imp, (SUM(C) / COUNT(C)) AS Cp FROM api_helper WHERE idUser = '$idPub' AND Date = '" . $DateTime1->format('Y-m-d') . "'";
							$q = $db2->query($sql);
							$sData = $db->fetch_array($q);
							
							$CPM = $sData['Cp'];
							$Imp = $sData['Imp'];
							
							echo "\n      <impressions>" . $Imp ."</impressions>";
							echo "\n      <ecpm currency=\"$CurrName\">" . number_format($CPM, 2, '.', '') . "</ecpm>";
						}
						
						echo "\n    </entry>";
					}
					$DateTime1->modify('+1 day');
				}
				
				echo "\n</dates>";
			}
		}elseif($_GET['by'] == 'device'){
			echo "\n  <devices>";
			
			foreach($Devices as $idDev => $NameDev){
				$sql = "SELECT SUM($Field) AS Rev FROM " . STATS .  " 
				INNER JOIN supplytag ON supplytag.id = " . STATS .  ".idTag 
				WHERE " . STATS .  ".idUser = '$idPub' AND supplytag.PlatformType = '$idDev' AND " . STATS .  ".Date BETWEEN '" . $DateTime1->format('Y-m-d') . "' AND '" . $DateTime2->format('Y-m-d') . "'";
				$Rev = $db->getOne($sql);
				
				if(round($Rev, 2, PHP_ROUND_HALF_DOWN) >= 0.01){
					echo "\n    <entry>";
					echo "\n      <device>" . $NameDev ."</device>";
					echo "\n      <revenue currency=\"$CurrName\">" . number_format($Rev, 2, '.', '') . "</revenue>";
					
					if($ApiData['Imp'] == 1){
						$sql = "SELECT SUM(Imp) AS Imp, (SUM(C) / COUNT(C)) AS Cp FROM api_helper 
						INNER JOIN supplytag ON supplytag.id = api_helper.idTag 
						WHERE api_helper.idUser = '$idPub' AND supplytag.PlatformType = '$idDev' AND api_helper.Date BETWEEN '" . $DateTime1->format('Y-m-d') . "' AND '" . $DateTime2->format('Y-m-d') . "'";
						//$Imp = $db->getOne($sql);
						//$CPM = $Rev / $Imp * 1000;
						$q = $db2->query($sql);
						$sData = $db->fetch_array($q);
						
						$CPM = $sData['Cp'];
						$Imp = $sData['Imp'];
						
						echo "\n      <impressions>" . $Imp ."</impressions>";
						echo "\n      <ecpm currency=\"$CurrName\">" . number_format($CPM, 2, '.', '') ."</ecpm>";
					}
					echo "\n    </entry>";
				}
			}
			
			echo "\n  </devices>\n";
		}elseif($_GET['by'] == 'site'){
			$sql2 = "SELECT * FROM sites WHERE idUser = '$idPub' AND deleted = 0 AND eric != 3 AND aproved = 0";
			
			$query2 = $db->query($sql2);
			if($db->num_rows($query2) > 0){
				echo "\n  <sites>";
				while($Site = $db->fetch_array($query2)){
					
					$idSite = $Site['id'];
					$sql = "SELECT SUM($Field) AS Rev FROM " . STATS .  " WHERE idUser = '$idPub' AND idSite = '$idSite' AND Date BETWEEN '" . $DateTime1->format('Y-m-d') . "' AND '" . $DateTime2->format('Y-m-d') . "'";
					$Rev = $db2->getOne($sql);
					
					if(round($Rev, 2, PHP_ROUND_HALF_DOWN) >= 0.01){
					
						echo "\n    <entry>";
						echo "\n      <site>" . $Site['sitename'] ."</site>";
						echo "\n      <revenue currency=\"$CurrName\">" . number_format($Rev, 2, '.', '') . "</revenue>";
						
						if($ApiData['Imp'] == 1){
							$sql = "SELECT SUM(api_helper.Imp) AS Imp, (SUM(C) / COUNT(C)) AS Cp FROM api_helper 
							INNER JOIN supplytag ON supplytag.id = api_helper.idTag 
							WHERE api_helper.idUser = '$idPub' AND supplytag.idSite = '$idSite' AND api_helper.Date BETWEEN '" . $DateTime1->format('Y-m-d') . "' AND '" . $DateTime2->format('Y-m-d') . "'";
							//$Imp = intval($db2->getOne($sql));
							$q = $db2->query($sql);
							$sData = $db->fetch_array($q);
							
							$CPM = $sData['Cp'];
							$Imp = $sData['Imp'];
							
							/*
							if($Rev > 0 && $Imp > 0){
								$CPM = $Rev / $Imp * 1000;
							}else{
								$CPM = '0.00';
							}
							*/
							
							echo "\n      <impressions>" . $Imp ."</impressions>";
							echo "\n      <ecpm currency=\"$CurrName\">" . number_format($CPM, 2, '.', '') ."</ecpm>";
						}
						
						echo "\n    </entry>";
					
					}
				}
				echo "\n  </sites>";
			}			
		}
	}else{
		
				
		$sql = "SELECT SUM($Field) AS Rev FROM " . STATS .  " WHERE idUser = '$idPub' AND Date BETWEEN '" . $DateTime1->format('Y-m-d') . "' AND '" . $DateTime2->format('Y-m-d') . "'";
		$Rev = $db->getOne($sql);
		echo "\n  <revenue currency=\"$CurrName\">" . number_format($Rev, 2, '.', '') . "</revenue>";
		
		if($ApiData['Imp'] == 1){
			$sql = "SELECT SUM(Imp) AS Imp, (SUM(C) / COUNT(C)) AS Cp FROM api_helper WHERE idUser = '$idPub' AND Date BETWEEN '" . $DateTime1->format('Y-m-d') . "' AND '" . $DateTime2->format('Y-m-d') . "'";
			$Imp = $db->getOne($sql);
			
			$CPM = $Rev / $Imp * 1000;
			
			echo "\n  <impressions>" . $Imp ."</impressions>";
			echo "\n  <ecpm currency=\"$CurrName\">" . number_format($CPM, 2, '.', '') . "</ecpm>";
		}


		
	}
	
	
	echo "\n</data>";
