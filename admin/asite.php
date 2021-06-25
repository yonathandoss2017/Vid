<?php
	session_start();
	define('CONST',1);
	require('../config.php');
	require('../db.php');
	require('../constantes.php');
	require('../common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	//echo 1;
	//exit(0);
	$sql = "SELECT * FROM emailing ORDER BY id DESC";
	$queryS = $db->query($sql);
	if($db->num_rows($queryS) > 0){
		while($Em = $db->fetch_array($queryS)){
			$sql = "SELECT * FROM " . SITES . " WHERE id = '" . $Em['idSite'] . "' ORDER BY id DESC";
			$query = $db->query($sql);
			$SiteI = $db->fetch_array($query);//
			echo $SiteI['sitename'];
			echo ': ';
			echo $SiteI['siteurl'];
			echo ' - ';
			if($Em['state'] == 1){
				echo '<span style="color:red;">Incompleto</span>';
			}else{
				echo '<span style="color:yellow;">No Encontradooo</span>';
			}
			echo '<br/>';
		}
	}
?>