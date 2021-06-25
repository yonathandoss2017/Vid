<?php	
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	require('/var/www/html/login/admin/libs/pub-managers.lib.php');
	$pm = new PM();
	
	$Date = date('Y-m-d');
	$DateC = date('Y-m-d', strtotime('-3 days'));
	
	//DOMINIOS REGULARES
	$sql = "SELECT * FROM " . SITES . " WHERE regular = 0 ";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Site = $db->fetch_array($query)){
			$idSite = $Site['id'];
			$SetRe = true;
			
			$sql = "SELECT SUM(formatLoads) AS FL, Date FROM " . STATS . " WHERE idSite = '$idSite' GROUP BY Date ORDER BY Date ASC";
			$query2 = $db->query($sql);
			if($db->num_rows($query2) > 0){
				$Count = 0;
				while($Stat = $db->fetch_array($query2)){
					if($Stat['FL'] >= 20000){
						$Count++;
						if($Count >= 20 && $SetRe === true){
							$arPer = explode('-',$Stat['Date']);
							$Month = intval($arPer[1]);
							$Period = $arPer[0] . '-' . $Month;
							$sql = "UPDATE " . SITES . " SET regular = '$Period' WHERE id = '$idSite' LIMIT 1";
							$db->query($sql);
							$SetRe = false;
						}
					}
				}
			}
		}
	}

	//DOMINIOS PREMIUM
	$sql = "SELECT * FROM " . SITES . " WHERE premium = 0";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Site = $db->fetch_array($query)){
			$idSite = $Site['id'];
			$SetPre = true;
			
			$sql = "SELECT SUM(formatLoads) AS FL, Date FROM " . STATS . " WHERE idSite = '$idSite' GROUP BY Date ORDER BY Date ASC";
			$query2 = $db->query($sql);
			if($db->num_rows($query2) > 0){
				$Count = 0;
				while($Stat = $db->fetch_array($query2)){
					if($Stat['FL'] >= 100000){
						$Count++;
						if($Count >= 20 && $SetPre === true){
							$arPer = explode('-',$Stat['Date']);
							$Month = intval($arPer[1]);
							$Period = $arPer[0] . '-' . $Month;
							echo $sql = "UPDATE " . SITES . " SET premium = '$Period' WHERE id = '$idSite' LIMIT 1";
							$db->query($sql);
							$SetPre = false;
						}
					}
				}
			}
		}
	}

	//DOMINIOS PERDIDOS
	$sql = "SELECT * FROM " . SITES . " WHERE regular != 0 AND lost = 0";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Site = $db->fetch_array($query)){
			$Count = 0;
			$idSite = $Site['id'];
			
			$sql = "SELECT SUM(formatLoads) AS FL, Date FROM " . STATS . " WHERE idSite = '$idSite' AND Date BETWEEN '$DateC' AND '$Date' GROUP BY Date ORDER BY Date ASC";
			$query2 = $db->query($sql);
			if($db->num_rows($query2) > 0){
				while($Stat = $db->fetch_array($query2)){
					
					if($Stat['FL'] < 20000){
						$Count++;
						if($Count >= 3){
							$arPer = explode('-',$Stat['Date']);
							$Month = intval($arPer[1]);
							$Period = $arPer[0] . '-' . $Month;
							$sql = "UPDATE " . SITES . " SET lost = '$Period' WHERE id = '$idSite' LIMIT 1";
							$db->query($sql);
						}
					}
				}
			}
		}
	}
	
	//REVISAR DOMINIOS PERDIDOS
	$sql = "SELECT * FROM " . SITES . " WHERE regular != 0 AND lost != 0";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Site = $db->fetch_array($query)){
			$idSite = $Site['id'];
			
			$sql = "SELECT SUM(formatLoads) AS FL, Date FROM " . STATS . " WHERE idSite = '$idSite' AND Date BETWEEN '$DateC' AND '$Date' GROUP BY Date ORDER BY Date ASC";
			$query2 = $db->query($sql);
			if($db->num_rows($query2) > 0){
				while($Stat = $db->fetch_array($query2)){
					
					if($Stat['FL'] >= 20000){
						$sql = "UPDATE " . SITES . " SET lost = 0 WHERE id = '$idSite' LIMIT 1";
						$db->query($sql);
					}
				}
			}
		}
	}
	
	
	//PUBLISHERS REGULARES
	$sql = "SELECT * FROM " . USERS . " WHERE regular = 0 ";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($User = $db->fetch_array($query)){
			$idUser = $User['id'];
			$SetRe = true;
			
			$sql = "SELECT SUM(formatLoads) AS FL, Date FROM " . STATS . " WHERE idUser = '$idUser' GROUP BY Date ORDER BY Date ASC";
			$query2 = $db->query($sql);
			if($db->num_rows($query2) > 0){
				$Count = 0;
				$Count2 = 0;
				while($Stat = $db->fetch_array($query2)){
					if($Stat['FL'] >= 20000){
						$Count++;
						if($Count >= 20 && $SetRe === true){
							$arPer = explode('-',$Stat['Date']);
							$Month = intval($arPer[1]);
							$Period = $arPer[0] . '-' . $Month;
							$sql = "UPDATE " . USERS . " SET regular = '$Period' WHERE id = '$idUser' LIMIT 1";
							$db->query($sql);
							$SetRe = false;
						}
					}
				}
			}
		}
	}
	
	//PUBLISHERS PREMIUM
	$sql = "SELECT * FROM " . USERS . " WHERE premium = 0 ";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($User = $db->fetch_array($query)){
			$idUser = $User['id'];
			$SetPre = true;
			
			$sql = "SELECT SUM(formatLoads) AS FL, Date FROM " . STATS . " WHERE idUser = '$idUser' GROUP BY Date ORDER BY Date ASC";
			$query2 = $db->query($sql);
			if($db->num_rows($query2) > 0){
				$Count = 0;
				$Count2 = 0;
				while($Stat = $db->fetch_array($query2)){
					if($Stat['FL'] >= 100000){
						$Count++;
						if($Count >= 20 && $SetPre === true){
							$arPer = explode('-',$Stat['Date']);
							$Month = intval($arPer[1]);
							$Period = $arPer[0] . '-' . $Month;
							$sql = "UPDATE " . USERS . " SET premium = '$Period' WHERE id = '$idUser' LIMIT 1";
							$db->query($sql);
							$SetPre = false;
						}
					}
				}
			}
		}
	}