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
	
	mysqli_set_charset($db->link,'utf8');
	mysqli_set_charset($db2->link,'utf8');

	$sql = "SELECT * FROM user 
	WHERE id >= 10000 AND (roles LIKE '%\"ROLE_PUBLISHER_MANAGER_HEAD\"%' || roles LIKE '%\"ROLE_PUBLISHER_MANAGER_SUB_HEAD\"%' || roles LIKE '%\"ROLE_ACCOUNT_MANAGER\"%') AND status = 1";
	$query2 = $db2->query($sql);
	if($db2->num_rows($query2) > 0){
		while($U = $db2->fetch_array($query2)){
			$idUser = $U['id'];
			
			$sql = "SELECT COUNT(*) FROM acc_managers WHERE id = '$idUser' LIMIT 1";		
			if($db->getOne($sql) == 0){
				
				$Date = date('Y-m-d');
				$Time = time();
				
				$User = $U['username'];
				$Email = $U['email'];
				$Pass = $U['password'];
				$Name = $U['name'];
				$Last = $U['last_name'];				
				$Nick = $U['nick'];
				$Own = intval($U['show_only_own_stats']);
				
				$sql = "INSERT INTO acc_managers (id, Email, Password, Name, Nick, Type, LastLogin, IP, Date, Time, Deleted, Follow, OwnStats) 
				VALUES ('$idUser', '$Email', '$Pass', '$Name $Last', '$Nick', 1, 0, '', '$Date', '$Time', 0, 1, $Own)";
				//echo "$sql<br/>";
				$db->query($sql);
				
				$sql = "INSERT INTO `users` (`id`, `user`, `nick`, `password`, `email`, `name`, `lastname`, `phone`, `movil`, `whatsapp`, `sykpe`, `ef`, `nifcif`, `company`, `country`, `province`, `city`, `cp`, `address`, `currency`, `lang`, `paymenttype`, `account`, `bankname`, `bankaddress`, `bankcountry`, `bankcurrency`, `iban`, `netterms`, `exceptions`, `swift`, `amount`, `LKQD_User`, `LKQD_id`, `SS_User`, `SS_id`, `lastlogin`, `lastinvoice`, `image`, `remember`, `showi`, `AccM`, `type`, `verified`, `integrate`, `verify_code`, `ip`, `campaing`, `keyword`, `regular`, `premium`, `lost`, `deleted`, `time`, `date`, `updated`) VALUES ($idUser, '-', '-', '-', '-', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'es', NULL, NULL, NULL, NULL, '0', '0', NULL, '60', '0', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, '', '0', '0', '0', '0', '', '', '', '', '0', '0', '0', '1', '', '', NULL);";
				$db->query($sql);
			}
		}
	}

	$DateTime = date('Y-m-d H:i:s', time() - 3000);
	$sql = "SELECT * FROM user 
	WHERE (roles LIKE '%\"ROLE_PUBLISHER_MANAGER_HEAD\"%' || roles LIKE '%\"ROLE_PUBLISHER_MANAGER_SUB_HEAD\"%' || roles LIKE '%\"ROLE_ACCOUNT_MANAGER\"%') AND updated_at >= '$DateTime'";// 
	$query2 = $db2->query($sql);
	if($db2->num_rows($query2) > 0){
		while($U = $db2->fetch_array($query2)){
			$idUser = $U['id'];
			
			$sql = "SELECT COUNT(*) FROM acc_managers WHERE id = '$idUser' LIMIT 1";		
			if($db->getOne($sql) > 0){
				
				$User = $U['username'];
				$Email = $U['email'];
				$Pass = $U['password'];
				$Name = $U['name'];
				$Last = $U['last_name'];				
				$Nick = $U['nick'];
				$status = $U['status'];
				$Own = intval($U['show_only_own_stats']);
				$Head = intval($U['publisher_manager_head_id']);
				if($Head == 0){
					$Head = $idUser;
				}
				
				$sql = "UPDATE acc_managers SET Email = '$Email', Name = '$Name $Last', Nick = '$Nick', OwnStats = '$Own', Head = $Head, Status = $status WHERE id = '$idUser' LIMIT 1";
				echo "$sql \n";
				$db->query($sql);
				
			}
		}
	}