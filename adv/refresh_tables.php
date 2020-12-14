<?php	
	@session_start();
	define('CONST',1);
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/reports_/libs/common_adv.php');
	
	$dbuser2 = "root";
	$dbpass2 = "vidooprod-pass_2020";
	//$dbhost2 = "aa4mgb1tsk2y6v.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbhost2 = "aa14extn6ty9ilx.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy-advertisers-panel";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	require('/var/www/html/login/reports_/adv/config.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	mysqli_set_charset($db->link,'utf8');
	mysqli_set_charset($db2->link,'utf8');
	
	$sql = "SELECT id FROM user ORDER BY id DESC LIMIT 1";
	$lastUser = intval($db->getOne($sql));

	
	$sql = "SELECT * FROM user WHERE id > $lastUser";
	$query2 = $db2->query($sql);
	if($db2->num_rows($query2) > 0){
		while($U = $db2->fetch_array($query2)){

			$id = $U['id'];
			$created_by_id = $U['created_by_id'];
			$updated_by_id = $U['updated_by_id'];
			$sales_manager_head_id = $U['sales_manager_head_id'];
			$user_id = $U['user_id'];
			$country_id = $U['country_id'];
			$username = $U['username'];
			$roles = $U['roles'];
			$password = $U['password'];
			$name = $U['name'];
			$last_name = $U['last_name'];
			$status = $U['status'];
			$locale = $U['locale'];
			$picture = $U['picture'];
			$created_at = $U['created_at'];
			$updated_at = $U['updated_at'];
			$ip_address = $U['ip_address'];
			$nick = $U['nick'];
			$monthly_target = $U['monthly_target'];
			$show_global_stats = $U['show_global_stats'];
			$phone = $U['phone'];
			$comments = $U['comments'];
			$email = $U['email'];
			$last_login = $U['last_login'];
			
			$sql = "INSERT INTO user (id, created_by_id, updated_by_id, sales_manager_head_id, user_id, country_id, username, roles, password, name, last_name, status, locale, picture, created_at, updated_at, ip_address, nick, monthly_target, show_global_stats, phone, comments, email, last_login) VALUES ('$id', '$created_by_id', '$updated_by_id', '$sales_manager_head_id', '$user_id', '$country_id', '$username', '$roles', '$password', '$name', '$last_name', '$status', '$locale', '$picture', '$created_at', '$updated_at', '$ip_address', '$nick', '$monthly_target', '$show_global_stats', '$phone', '$comments', '$email', '$last_login')";
			$db->query($sql);
		}
	}
	
	$sql = "SELECT * FROM user WHERE id <= $lastUser";
	$query2 = $db2->query($sql);
	if($db2->num_rows($query2) > 0){
		while($U = $db2->fetch_array($query2)){

			$idU = $U['id'];
			$sales_manager_head_id = $U['sales_manager_head_id'];
			$name = $U['name'];
			$last_name = $U['last_name'];
			$status = $U['status'];
			$nick = $U['nick'];
			
			$sql = "UPDATE user SET sales_manager_head_id = '$sales_manager_head_id', name = '$name', last_name = '$last_name', status = '$status', nick = '$nick' WHERE id = '$idU' LIMIT 1";
			$db->query($sql);
		}
	}
	
	$sql = "SELECT id FROM ssp ORDER BY id DESC LIMIT 1";
	$lastSSP = intval($db->getOne($sql));
	
	$sql = "SELECT * FROM ssp WHERE id >= $lastSSP";
	$query2 = $db2->query($sql);
	if($db2->num_rows($query2) > 0){
		while($S = $db2->fetch_array($query2)){
			$id = $S['id'];
			$name = $S['name'];
			$deleted = $S['deleted'];

			$sql = "INSERT INTO ssp (id, name, deleted) VALUES ('$id', '$name', '$deleted')";
			$db->query($sql);
		}
	}
	
	$sql = "SELECT id FROM dsp ORDER BY id DESC LIMIT 1";
	$lastDSP = intval($db->getOne($sql));
	
	$sql = "SELECT * FROM dsp WHERE id >= $lastDSP";
	$query2 = $db2->query($sql);
	if($db2->num_rows($query2) > 0){
		while($S = $db2->fetch_array($query2)){
			$id = $S['id'];
			$name = $S['name'];
			$created_at = $S['created_at'];
			$deleted = $S['deleted'];

			$sql = "INSERT INTO dsp (id, name, created_at, deleted) VALUES ('$id', '$name', '$created_at', '$deleted')";
			$db->query($sql);
		}
	}
	
	$sql = "SELECT id FROM advertiser ORDER BY id DESC LIMIT 1";
	$lastAdv = intval($db->getOne($sql));
	
	$sql = "SELECT * FROM advertiser WHERE id > $lastAdv";
	$query2 = $db2->query($sql);
	if($db2->num_rows($query2) > 0){
		while($S = $db2->fetch_array($query2)){
			$id = $S['id'];
			$name = $S['name'];
			$created_at = $S['created_at'];
			$deleted = $S['deleted'];

			$sql = "INSERT INTO advertiser (id, name, created_at, deleted) VALUES ('$id', '$name', '$created_at', '$deleted')";
			$db->query($sql);
		}
	}
			
	
	$sql = "SELECT id FROM agency ORDER BY id DESC LIMIT 1";
	$lastAge = intval($db->getOne($sql));
	
	$sql = "SELECT * FROM agency WHERE id >= $lastAge";
	$query2 = $db2->query($sql);
	if($db2->num_rows($query2) > 0){
		while($S = $db2->fetch_array($query2)){
			$id = $S['id'];
			$sales_manager_id = $S['sales_manager_id'];
			$name = $S['name'];
			$type = $S['type'];
			$rebate = $S['rebate'];
			$details = $S['details'];
			$account_manager = $S['account_manager'];
			$deleted = $S['deleted'];
			
			$sql = "INSERT INTO agency (id, sales_manager_id, name, type, rebate, details, account_manager, deleted) VALUES ('$id', '$sales_manager_id', '$name', '$type', '$rebate', '$details', '$account_manager', '$deleted')";
			$db->query($sql);
		}
	}
			
	$sql = "SELECT * FROM agency WHERE id < $lastAge";
	$query2 = $db2->query($sql);
	if($db2->num_rows($query2) > 0){
		while($S = $db2->fetch_array($query2)){
			$id = $S['id'];
			$sales_manager_id = $S['sales_manager_id'];
			$name = $S['name'];
			$type = $S['type'];
			$rebate = $S['rebate'];
			$account_manager = $S['account_manager'];
			$deleted = $S['deleted'];
			
			$sql = "UPDATE agency SET 
			sales_manager_id = $sales_manager_id,
			name = '$name',
			type = $type,
			rebate = '$rebate',
			account_manager = '$account_manager',
			deleted = $deleted
			WHERE id = $id LIMIT 1";
			$db->query($sql);
		}
	}
	
	$sql = "SELECT id FROM campaign ORDER BY id DESC LIMIT 1";
	$lastCamp = intval($db->getOne($sql));
	
	$sql = "SELECT * FROM campaign WHERE id > $lastCamp";
	$query2 = $db2->query($sql);
	if($db2->num_rows($query2) > 0){
		while($S = $db2->fetch_array($query2)){
			$id = $S['id'];
			$agency_id = $S['agency_id'];
			$advertiser_id = $S['advertiser_id'];
			$ssp_id = $S['ssp_id'];
			$dsp_id = $S['dsp_id'];
			$name = $S['name'];

			$name = mysqli_real_escape_string($db->link, $name);
			
			$type = $S['type'];
			$deal_id = $S['deal_id'];
			$vtr = $S['vtr'];
			$viewability = $S['viewability'];
			$ctr = $S['ctr'];
			$volume = $S['volume'];
			$list_type = $S['list_type'];
			$details = $S['details'];
			$cpm = $S['cpm'];
			$start_at = $S['start_at'];
			$end_at = $S['end_at'];
			$rebate = $S['rebate'];
			$status = $S['status'];
			$created_at = $S['created_at'];
			
			$deleted = $S['deleted'];
			
			$sql = "INSERT INTO campaign (id, agency_id, advertiser_id, ssp_id, dsp_id, name, type, deal_id, vtr, viewability, ctr, volume, list_type, details, cpm, start_at, end_at, rebate, status, created_at, deleted)
			VALUES ('$id', '$agency_id', '$advertiser_id', '$ssp_id', '$dsp_id', '$name', '$type', '$deal_id', '$vtr', '$viewability', '$ctr', '$volume', '$list_type', '$details', '$cpm', '$start_at', '$end_at', '$rebate', '$status', '$created_at', '$deleted')";
			$db->query($sql);
		}
	}
	
	$sql = "SELECT * FROM campaign WHERE id <= $lastCamp";
	$query2 = $db2->query($sql);
	if($db2->num_rows($query2) > 0){
		while($S = $db2->fetch_array($query2)){
			$name = $S['name'];
			$idC = $S['id'];
			$advertiser_id = $S['advertiser_id'];
			$agency_id = $S['agency_id'];
			$deal_id = $S['deal_id'];
			
			$sql = "UPDATE campaign SET name = '$name', advertiser_id = '$advertiser_id', agency_id = '$agency_id', deal_id = '$deal_id' WHERE id = '$idC' LIMIT 1 ";
			$db->query($sql);
		}
	}
	
	$sql = "TRUNCATE campaign_country";
	$db->query($sql);
	
	$result = mysqli_query($db2->link, "SELECT * FROM campaign_country");
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$sql = "INSERT INTO campaign_country (".implode(", ",array_keys($row)).") VALUES ('".implode("', '",array_values($row))."')";
	    mysqli_query($db->link, $sql);
	}