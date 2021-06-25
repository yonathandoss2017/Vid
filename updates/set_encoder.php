<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$dbuser2 = "root";
	$dbpass2 = "Jz8eDbamcNx3TskWzrjzH7g";
	$dbhost2 = "vidoomy-production.cpijmqdfbof9.eu-west-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	$db3 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	
	$sql = "SELECT * FROM user WHERE encoder = ''";
	$query = $db2->query($sql);
	if($db2->num_rows($query) > 0){
		while($U = $db2->fetch_array($query)){
			$idUser = $U['id'];
			
			echo "ID $idUser ";
			
			$sql = "SELECT password FROM user WHERE id = $idUser LIMIT 1";
			$PassL = strlen($db2->getOne($sql));
			
			if($PassL == 32){
				$sql = "UPDATE user SET encoder = 'old' WHERE id = $idUser LIMIT 1";
				$db3->query($sql);
				echo "old";
			}else{
				$sql = "UPDATE user SET encoder = 'new' WHERE id = $idUser LIMIT 1";
				$db3->query($sql);
				echo "new";
			}
			
			$sql = "UPDATE users SET enable_new = 1 WHERE id = $idUser LIMIT 1";
			$db->query($sql);
			
			echo " F \n";
			
	    }
	    fclose($handle);
	} else {
	    echo "error opening the file.";
	} 
	