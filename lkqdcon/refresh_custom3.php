<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('../config.php');
	require('../constantes.php');
	require('../db.php');
	require('../common.lib.php');
	require('../admin/libs/display.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	
	$sql = "SELECT sites.id AS idSite, sites.idUser AS idUser, sites.filename AS filename, users.LKQD_id as LKQD_id, users.user AS user, users.AccM AS AccM  FROM sites
	INNER JOIN users ON users.id = sites.idUser
	WHERE  users.deleted != 1 AND users.AccM != 9999  AND users.AccM != 15 AND filename != '' AND sites.deleted != 1 ORDER BY users.id ASC"; // 
	$query = $db->query($sql);
	while($Site = $db->fetch_array($query)){
		$idSite = $Site['idSite'];
		
		echo "<br/> $idSite: " . $Site['filename'];
		
		newGenerateJS($idSite);
		//exit();
	}
	
	
	
	
	
	
	
	/*
	$sql = "SELECT sites.id AS idSite, sites.idUser AS idUser, sites.filename AS filename, users.LKQD_id as LKQD_id, users.user AS user, users.AccM AS AccM  FROM sites
	INNER JOIN users ON users.id = sites.idUser
	WHERE  users.deleted != 1 AND users.AccM != 9999  AND users.AccM != 15 AND filename != '' AND sites.deleted != 1 ORDER BY users.id ASC"; // 
	$query = $db->query($sql);
	
	//echo "User ID, Nombre de Usuario, Tag, Account Manager \n";
	//exit;
	$Nsite = 0;
	while($Site = $db->fetch_array($query)){
		$Nsite++;
		$idSite = $Site['idSite'];
		echo $idSite . ': ';
		$idUser = $Site['idUser'];
		$Filename = $Site['filename'];
		$User = $Site['user'];
		$idAccm = $Site['AccM'];
		
		$sql = "SELECT Name FROM acc_managers WHERE id = '$idAccm' LIMIT 1";
		$AccM = $db->getOne($sql);
		if($AccM == ''){
			$AccM = 'No asignado';
		}
		
		$PartnerID = $Site['LKQD_id'];
		if($PartnerID != '' && intval($PartnerID) != 0){
			//echo "SI: $PartnerID Filename $Filename \n";
			echo 1;
			$NewCustom3 = "custom3: '1.0,1!vidoomy.com,".$PartnerID.",1,,',";
			$OldCustom3 = "custom3: '1.0,1!vidoomy.com,".$PartnerID.",1,',";
			
			$sql = "SELECT * FROM ads WHERE idSite = '$idSite'";
			$query2 = $db->query($sql);
			while($Ad = $db->fetch_array($query2)){
				$idAd = $Ad['id'];
				if($Ad['CCode'] != ''){
					echo 2;
					if(strpos($Ad['CCode'], 'custom3') !== false){
						echo 3;
						$NewCode = str_replace($OldCustom3, $NewCustom3, $Ad['CCode']);
						$NewCode = mysqli_real_escape_string($db->link, $NewCode);
						$sql = "UPDATE ads SET CCode = '$NewCode' WHERE id = '$idAd' LIMIT 1";
						$db->query($sql);
						echo "UPDATED C Code\n";
					}
				}
			}
			
			newGenerateJS($idSite);
			//newGenerateJS(297);
			
			echo "GENERATED JS <br/>";
			
			//exit(0);
			
			if($Nsite >= 4000){
				exit(0);
				echo 'EXITTTTTTTTTTTTT';
			}
		}else{
			echo "$idUser, $User, $Filename, $AccM <br/>";
		}
		
		//
		
	}
	
	*/