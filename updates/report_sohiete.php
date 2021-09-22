<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/common.lib.php');
	require('/var/www/html/login/admin/libs/display.lib.php');
	
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	$db2 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);
	
	$Users = array();
	$UsersNew = array();
	$UsersOld = array();

	$sql = "SELECT DISTINCT(idUser) AS idUser, SUM(Revenue) AS Rev FROM stats WHERE Date >= '2020-01-01' GROUP BY idUser ORDER BY Rev DESC";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($U = $db->fetch_array($query)){
			if($U['Rev'] > 0){
				$Users[] = $U['idUser'];
			}
		}
	}
	
	
	$sql = "SELECT DISTINCT(idUser) AS idUser, SUM(Revenue) AS Rev FROM stats WHERE Date < '2020-01-01' GROUP BY idUser ORDER BY Rev DESC";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($U = $db->fetch_array($query)){
			if($U['Rev'] > 0){
				$UsersOld[] = $U['idUser'];
			}
		}
	}
	
	
	
	foreach($Users as $uId){
		if(!in_array($uId, $UsersOld)){
			$UsersNew[] = $uId;
		}
	}
	
	//print_r($UsersNew);
	echo "Usuario,Pais,Publisher Manager,Net Terms,Financial Email \n";
	foreach($UsersNew as $idU){
		$sql = "SELECT user.username, country.nicename, publisher.account_manager_id, publisher.net_terms, publisher.contacts FROM user
		INNER JOIN publisher ON publisher.user_id = user.id 
		INNER JOIN country ON publisher.country_id = country.id
		WHERE user.id = $idU";
		$query = $db2->query($sql);
		if($db2->num_rows($query) > 0){
			$U = $db2->fetch_array($query);
			//print_r($U);
			
			$FEmail = '';
			$Contacts = json_decode($U['contacts']);
			if(count($Contacts) > 0){
				foreach($Contacts as $C){
					$FEmail = $C->email;
				}
			}
			
			$sql = "SELECT CONCAT(name, ' ', last_name) FROM user WHERE id = '" . $U['account_manager_id'] . "'";
			$Manager = $db2->getOne($sql);
			
			$Username = $U['username'];
			$Country = $U['nicename'];
			$NetTerms = $U['net_terms'];
			echo "$Username,$Country,$Manager,$NetTerms,$FEmail \n";
		}
	}