<?php
	session_start();
	define('CONST',1);
	require('db.php');
	require('config.php');

	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	$db2 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);
	
	
	if(isset($_GET['idu']) && isset($_GET['t'])){
		$idUser = intval($_GET['idu']);
		$token = mysqli_real_escape_string($db2->link, $_GET['t']);
		
		$sql = "SELECT id FROM loginoldpanel WHERE token = '$token' AND user_id = '$idUser' AND used = 0";
		$idToken = $db2->getOne($sql);
		if($idToken > 0){
			$sql = "UPDATE loginoldpanel SET used = 1 WHERE id = '$idToken' LIMIT 1";
			$db2->query($sql);
			
			$sql = "SELECT enable_new FROM users WHERE id = '$idUser' LIMIT 1";
			$EnableN = $db->getOne($sql);
			
			if($EnableN == 1){
				$sql = "SELECT user FROM users WHERE id = '$idUser' LIMIT 1";
				$User = $db->getOne($sql);
				
				header('Location: https://newlogin.vidoomy.com/?_switch_user=' . $User);
				exit(0);
			}else{
				$_SESSION['login'] = $idUser;
				
				header('Location: stats.php');
			}
		}else{
			header('Location: index.php');
			exit(0);
		}		
	}else{
		header('Location: index.php');
		exit(0);
	}
?>