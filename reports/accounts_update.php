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
	
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$dbuser2 = "root";
	$dbpass2 = "Jz8eDbamcNx3TskWzrjzH7g";
	$dbhost2 = "vidoomy-production.cpijmqdfbof9.eu-west-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	$db3 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	$sql = "SELECT user.id, user.username, publisher.account_manager_id FROM user 
	INNER JOIN publisher ON user.id = publisher.user_id
	WHERE user.roles LIKE '%ROLE_PUBLISHER\";}'";
	$query = $db2->query($sql);
	
	$Nq = 0;
	$sqlLong = '';
	
	while($Publisher = $db2->fetch_array($query)){
		
		$iPub = $Publisher['id'];
		$pubName = $Publisher['username'];
		$idManager = $Publisher['account_manager_id'];
		
		$Updated = date('Y-m-d H:i:s');
		
		if($idManager > 0){
			$sql = "SELECT * FROM user WHERE id = $idManager LIMIT 1";
			$query3 = $db3->query($sql);
			$ManagerData = $db3->fetch_array($query3);
			//$nameManager = $ManagerData['username'];
			$nameManager = $ManagerData['name'] . ' ' . $ManagerData['last_name'];
			
			$ManagerRoles = unserialize($ManagerData['roles']);
			//print_r($ManagerRoles);
			
			//ROLE_PUBLISHER_MANAGER_SUB_HEAD
			//ROLE_PUBLISHER_MANAGER_HEAD
			
			if(in_array('ROLE_PUBLISHER_MANAGER_HEAD', $ManagerRoles)){
				$idLead = $idManager;
				$nameLead = $nameManager;
				
				$idSubLead = $idManager;
				$nameSubLead = $nameManager;
			}elseif(in_array('ROLE_PUBLISHER_MANAGER_SUB_HEAD', $ManagerRoles)){
				$idSubLead = $idManager;
				$nameSubLead = $nameManager;
				
				$idLead = $ManagerData['manager_id'];
				if($idLead > 0){
					$sql = "SELECT * FROM user WHERE id = $idLead LIMIT 1";
					$query3 = $db3->query($sql);
					$LeadData = $db3->fetch_array($query3);
					//$nameLead = $LeadData['username'];
					$nameLead = $LeadData['name'] . ' ' . $LeadData['last_name'];
				}else{
					$idLead = 0;
					$nameLead = 'NA';
				}
			}else{
				$idSubLead = $ManagerData['manager_id'];
				if($idSubLead > 0){
				
					$sql = "SELECT * FROM user WHERE id = $idSubLead LIMIT 1";
					$query3 = $db3->query($sql);
					$SubLeadData = $db3->fetch_array($query3);
					//$nameSubLead = $SubLeadData['username'];
					$nameSubLead = $SubLeadData['name'] . ' ' . $SubLeadData['last_name'];
					
					$SubLeadRoles = unserialize($SubLeadData['roles']);
					if(in_array('ROLE_PUBLISHER_MANAGER_HEAD', $SubLeadRoles)){
						$idLead = $idSubLead;
						$nameLead = $nameSubLead;
					}else{
						$idLead = $SubLeadData['manager_id'];
					
						$sql = "SELECT * FROM user WHERE id = $idLead LIMIT 1";
						$query3 = $db3->query($sql);
						$LeadData = $db3->fetch_array($query3);
						//$nameLead = $LeadData['username'];
						$nameLead = $LeadData['name'] . ' ' . $LeadData['last_name'];
					}
				}else{
					$idLead = 0;
					$nameLead = 'NA';
					$idSubLead = 0;
					$nameSubLead = 'NA';
				}
			}
		}
		
		$sql = "SELECT id FROM accounts WHERE idUser = $iPub LIMIT 1";
		$idA = $db->getOne($sql);
		
		if($idA <= 0){
			$sql = "INSERT INTO accounts (idUser, publisherName, idManager, nameManager, idSubLead, nameSubLead, idLead, nameLead, updated)
			VALUES ($iPub, '$pubName', '$idManager', '$nameManager', '$idSubLead', '$nameSubLead', '$idLead', '$nameLead', '$Updated'); ";
		}else{
			$sql = "UPDATE accounts SET publisherName = '$pubName', idManager = $idManager, nameManager = '$nameManager', idSubLead = $idSubLead, nameSubLead = '$nameSubLead', idLead = $idLead, nameLead = '$nameLead', updated = '$Updated'
			WHERE id = $idA LIMIT 1; ";			
		}
		
		$db->query($sql);
		//echo $sql . "\n";
		
	}