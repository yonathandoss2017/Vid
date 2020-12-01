<?php
	define('CONST',1);
	require('../config.php');
	require('../constantes.php');
	require('../db.php');
	require('../common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$arrayTags = array();
	if(isset($_POST['filter']) && isset($_POST['search'])){
		$Search = $_POST['search'];
				
		if($_POST['filter'] == 'Supply Source'){
			mysqli_set_charset($db->link, "utf8");
			$sql = "SELECT id, TagName FROM supplytag WHERE TagName LIKE '%$Search%' ORDER BY id ASC LIMIT 6";
			$query = $db->query($sql);
			if($db->num_rows($query) > 0){
				$n = 0;
				while($supplyTag = $db->fetch_array($query)){
					$n++;
					$arrayTags[$n]['id'] = $supplyTag['id'];
					$arrayTags[$n]['tagName'] = $supplyTag['TagName'];
					//echo $Tag['TagName'];
				}
			}
		}
		
		if($_POST['filter'] == 'Supply Partner'){
			mysqli_set_charset($db->link, "utf8");
			$sql = "SELECT id, user FROM users WHERE user LIKE '%$Search%' ORDER BY id ASC LIMIT 6";
			$query = $db->query($sql);
			if($db->num_rows($query) > 0){
				$n = 0;
				while($supplyPartner = $db->fetch_array($query)){
					$n++;
					$arrayTags[$n]['id'] = $supplyPartner['id'];
					$arrayTags[$n]['tagName'] = $supplyPartner['user'];
					//echo $Tag['TagName'];
				}
			}
		}
		
		if($_POST['filter'] == 'Country'){
			mysqli_set_charset($db->link, "utf8");
			$sql = "SELECT id, Name FROM reports_country_names WHERE Name LIKE '%$Search%' ORDER BY id ASC LIMIT 6";
			$query = $db->query($sql);
			if($db->num_rows($query) > 0){
				$n = 0;
				while($country = $db->fetch_array($query)){
					$n++;
					$arrayTags[$n]['id'] = $country['id'];
					$arrayTags[$n]['tagName'] = $country['Name'];
					//echo $Tag['TagName'];
				}
			}
		}
		
		if($_POST['filter'] == 'Domain'){
			mysqli_set_charset($db->link, "utf8");
			$sql = "SELECT id, Name FROM reports_domain_names WHERE Name LIKE '%$Search%' ORDER BY id ASC LIMIT 6";
			$query = $db->query($sql);
			if($db->num_rows($query) > 0){
				$n = 0;
				while($domain = $db->fetch_array($query)){
					$n++;
					$arrayTags[$n]['id'] = $domain['id'];
					$arrayTags[$n]['tagName'] = $domain['Name'];
					//echo $Tag['TagName'];
				}
			}
		}
		//echo json_encode($arrayTags);
		foreach($arrayTags as $Tag){
			echo '<div class="single-res">' . $Tag['tagName'] . '</div>';
		}
	}