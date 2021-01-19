<?php	
	@session_start();
	define('CONST',1);
	require('../../db.php');
	require('../libs/common_adv.php');
	
	$mem_var = new Memcached('reps');
	$mem_var->addServer("localhost", 11211);

	if(!isset($_POST['uuid']) || !isset($_POST['env'])){
		header('HTTP/1.0 403 Forbidden');
		echo 'Access denieddd';
		exit(0);
	}
	
	if($_POST['env'] == 'pre' || $_POST['env'] == 'dev'){
		$dbuser2 = "root";
		$dbpass2 = "vidoopre-pass_2020";
		//$dbhost2 = "aa1nh4ao2doeo1w.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
		$dbhost2 = "aazw79txt1iy6x.cme5dsqa4tew.us-east-2.rds.amazonaws.com";
		$dbname2 = "vidoomy-advertisers-panel";
		$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
		
		require('config_pre.php');
		$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	}elseif($_POST['env'] == 'prod'){
		$dbuser2 = "root";
		$dbpass2 = "vidooprod-pass_2020";
		//$dbhost2 = "aa4mgb1tsk2y6v.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
		$dbhost2 = "aa14extn6ty9ilx.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
		$dbname2 = "vidoomy-advertisers-panel";
		$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
		
		require('config.php');
		$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	}else{
		header($_SERVER["SERVER_PROTOCOL"] . ' Wrong ENV', true, 500);
		exit(0);
	}
	
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	header("Allow: GET, POST, OPTIONS, PUT, DELETE");
	$method = $_SERVER['REQUEST_METHOD'];
	if($method == "OPTIONS") {
	    die();
	}
	
	mysqli_set_charset($db->link,'utf8');
	mysqli_set_charset($db2->link,'utf8');

	$UUID = mysqli_real_escape_string($db2->link, $_POST['uuid']);
	
	$sql = "SELECT report_key.*, user.roles AS URoles FROM report_key INNER JOIN user ON user.id = report_key.user_id WHERE report_key.unique_id = '$UUID' LIMIT 1";//AND report_key.status = 0
	$query = $db2->query($sql);
	if($db2->num_rows($query) > 0){
		$Repo = $db2->fetch_array($query);
		$RepId = $Repo['id'];
		$UserId = $Repo['user_id'];
		//$SOOS = $Repo['show_only_own_stats'];
		$RolesJSON = json_decode($Repo['URoles']);
		
		/*if(is_array($Roles)){
			if(in_array('ROLE_ADVERTISER', $Roles)){
				$AdvRepo = true;
			}
		}*/
		
		$sql = "SELECT Name FROM user WHERE id = '$UserId' LIMIT 1";
		$UserName = $db->getOne($sql);
		$sql = "UPDATE report_key SET status = 1 WHERE id = '$RepId' LIMIT 1";
		$db2->query($sql);
	}else{
		header('HTTP/1.0 403 Forbidden');
		echo 'Access denied';
		exit(0);
	}
	//$UserId = 3;
	
	//$sql = "SELECT roles FROM user WHERE id = $UserId LIMIT 1";
	//$Roles = $db->getOne($sql);
	
	//$RolesJSON = json_decode($Roles);
	
	$AdvRep = false;
	if(in_array('ROLE_ADMIN', $RolesJSON)){
		//echo 'ADMIN';
		$PubManFilter = "";
	}elseif(in_array('ROLE_ADVERTISER', $RolesJSON)){
		$sql = "SELECT id FROM advertiser WHERE user_id = $UserId LIMIT 1";
		$AdvID = $db2->getOne($sql);
		$PubManFilter = " AND campaign.advertiser_id = $AdvID AND {ReportsTable}.Impressions > 0 ";
		
		$AdvRep = true;
		
		if(isset($_POST['Dimensions'])){
			$Dimensions = $_POST['Dimensions'];
			if(is_array($Dimensions)){
				foreach($_POST['Dimensions'] as $K => $D){
					$_POST['Dimensions'][$K] = 'campaign_name';
				}
			}
		}
	}elseif(in_array('ROLE_CAMPAIGN_VIEWER', $RolesJSON)){
		$sql = "SELECT * FROM campaign_viewer_campaigns WHERE user_id = '$UserId'";
		$queryS = $db2->query($sql);
		if($db2->num_rows($queryS) > 0){
			$PubManFilter = " AND (";
			$OrC = "";
			while($U = $db2->fetch_array($queryS)){
				$idC = $U['campaign_id'];
				$PubManFilter .= $OrC . "campaign.id = '$idC'";
				$OrC = " OR ";
			}
			$PubManFilter .= ")";
		}else{
			$PubManFilter = " AND campaign.id = 0 ";
		}
	}elseif(in_array('ROLE_ACCOUNT_MANAGER', $RolesJSON)){
		$sql = "SELECT * FROM account_manager_campaigns WHERE user_id = '$UserId'";
		$queryS = $db2->query($sql);
		if($db2->num_rows($queryS) > 0){
			$PubManFilter = " AND (";
			$OrC = "";
			while($U = $db2->fetch_array($queryS)){
				$idC = $U['campaign_id'];
				$PubManFilter .= $OrC . "campaign.id = '$idC'";
				$OrC = " OR ";
			}
			$PubManFilter .= ")";
		}else{
			$PubManFilter = " AND campaign.id = 0";
		}
	}else{
		if(in_array('ROLE_SALES_MANAGER_HEAD', $RolesJSON)){
			//echo 'HEAD';
			$PubManFilter = " AND (agency.sales_manager_id = '$UserId'";
			$sql = "SELECT id FROM user WHERE sales_manager_head_id = '$UserId'";
			$queryS = $db2->query($sql);
			if($db2->num_rows($queryS) > 0){
				while($U = $db2->fetch_array($queryS)){
					$idS = $U['id'];
					$PubManFilter .= " OR agency.sales_manager_id = '$idS' ";
				}
			}
			$PubManFilter .= ")";
		}else{
			//echo 'SALES';
			$PubManFilter = " AND agency.sales_manager_id = '$UserId' ";
		}
	}
	
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
			
			$sql = "UPDATE campaign SET name = '$name', advertiser_id = '$advertiser_id', agency_id = '$agency_id' WHERE id = '$idC' LIMIT 1 ";
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
			
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	header("Allow: GET, POST, OPTIONS, PUT, DELETE");
	$method = $_SERVER['REQUEST_METHOD'];
	if($method == "OPTIONS") {
	    die();
	}
	
	$Data = array();
	$DatesOK = false;
	$MetricsOK = false;
	$DimensionsOK = false;
	$TypeOK = false;
	$IncludeTime = false;
	$AddDimensions = false;
	$TimeName = "";
	$ToFilter = array();
	$StartMonth = '';
	$EndMonth = '';
	$ReportsTable = '';
	$UnionTables = array();
	$Overall = false;
	$UsedInnerJ = array();
	$ThereAreFilters = false;
	
	if(isset($_POST['PDate'])){
		$Dates = $_POST['PDate'];
		if(is_array($Dates)){
			if(count($Dates) == 2){
				$DateFrom = DateTime::createFromFormat('d/m/Y', $Dates[0]);
				$DFrom = $DateFrom->format('Y-m-d');
				$StartMonth = $DateFrom->format('Ym');
				$DateTo = DateTime::createFromFormat('d/m/Y', $Dates[1]);
				$DTo = $DateTo->format('Y-m-d');
				$EndMonth = $DateTo->format('Ym');
				$DatesOK = true;						
			}
		}
	}
	
	if(isset($_POST['Dimensions'])){
		$Dimensions = $_POST['Dimensions'];
		if(!is_array($Dimensions)){
			$Dimensions = array();
		}
	}else{
		$Dimensions = array();
	}
	$DimensionsOK = true;
	
	if(isset($_POST['reportType'])){
		$TypeOK = true;
		$RepType = $_POST['reportType'];
		//$RepType = 'overall';
		
		if($RepType != 'hourly'){
			$BaseTable = 'reports_resume';
		}else{
			$BaseTable = 'reports';
		}
		if($RepType != 'overall' || count($Dimensions) == 0){
			$IncludeTime = true;
		}
		if($RepType == 'overall'){
			$Overall = true;
		}
		
		
		//$NewTable = $BaseTable . $StartMonth;
		$NewTable = 'reports';
		if(checkTableExists($NewTable)){
			$UnionTables[] = $NewTable;
		}
		/*
		if($StartMonth != $EndMonth){
			$start = $DateFrom->modify('first day of this month');
			$end = $DateTo->modify('first day of next month');
			$interval = DateInterval::createFromDateString('1 month');
			$period = new DatePeriod($start, $interval, $end);
			
			foreach ($period as $dt) {
				$NewTable = $BaseTable . $dt->format("Ym");
				if(!in_array($NewTable, $UnionTables)){
					if(checkTableExists($NewTable)){
						$UnionTables[] = $NewTable;
					}
			    }
			}
		}
		*/
	}else{
		$TypeOK = true;
	}
	
	$CSVResponse = false;
	if(isset($_POST['csv'])){
		if($_POST['csv'] == 'true'){
			$CSVResponse = true;
		}
	}
	
	//print_r($UnionTables);
	//exit(0);
	if($DatesOK && $TypeOK){
		if(isset($_POST['Metrics'])){
			$Metrics = $_POST['Metrics'];
			if(is_array($Metrics)){
				if(count($Metrics) > 0){
					$MetricsOK = true;
				}
			}
		}
	}
	
	//print_r($_POST);
	if($DimensionsOK){
		if(isset($_POST['Filters'])){
			if(is_array($_POST['Filters'])){
				foreach($_POST['Filters'] as $Fi){				
					if(isset($Fi['label']) && isset($Fi['value']) && isset($Fi['include'])){
						if($Fi['label'] != '' && $Fi['value'] != ''){
							$ToFilter[$Fi['include']][$Fi['label']][] = $Fi['value'];
						}
					}
				}
			}
		}
		
		$OrderC = array();
		if(isset($_POST['order'])){
			if(is_array($_POST['order'])){
				foreach($_POST['order'] as $O){
					$OrderC[$O['column']] = $O['dir'];
				}
			}
		}
		
		if(isset($_POST['length'])){
			$Length = intval($_POST['length']);
		}else{
			$Length = false;
		}
		
		if(isset($_POST['start'])){
			$Start = intval($_POST['start']);
		}else{
			$Start = false;
		}
		
		$No = 0;
		$OrderName = "";
		$OrderParam = "";
		$OrderComa = "";
		$CGroup = "GROUP BY ";
		$C = "";
		$SQLDimensions = "";
		$SQLDimensionsOverall = "";
		$SQLInnerJoins = "";
		$SQLGroups = "";
		
		if($IncludeTime){
			
			$SQLDimensions .= $TimesSQL[$RepType]['Name'];
			$SQLDimensionsOverall .= $TimesSQL[$RepType]['Name'];
			if($TimesSQL[$RepType]['GroupBy'] !== false){
				$SQLGroups = "GROUP BY ";
				$SQLGroups .= $TimesSQL[$RepType]['GroupBy'];
				$CGroup = ", ";
			}
			
			if(count($OrderC) > 0){
				if(array_key_exists($No, $OrderC)){
					$OrderName = $TimesSQL[$RepType]['OrderVal'];
					$OrderParam .= $OrderComa . $OrderName . " " . $OrderC[$No];
					$OrderComa = ", ";
				}
			}
			$TimeName = $TimesSQL[$RepType]['ShowName'];
			
			$No++;
			$C = ", ";
		}
		
		if(count($Dimensions) > 0){
			$AddDimensions = true;
			
			foreach($Dimensions as $DimensionName){

				$SQLDimensions .= $C . $DimensionsSQL[$DimensionName]['Name'];
				$SQLDimensionsOverall .= $C . 'R.' . $DimensionsSQL[$DimensionName]['GroupBy'] . " AS " . $DimensionsSQL[$DimensionName]['GroupBy'];
				
				if(count($DimensionsSQL[$DimensionName]['InnerJoin']) > 0){
					foreach($DimensionsSQL[$DimensionName]['InnerJoin'] as $JoinTable => $JoinSQL){
						if(!in_array($JoinTable, $UsedInnerJ)) {
							$SQLInnerJoins .= $JoinSQL;
							$UsedInnerJ[] = $JoinTable;
						}
					}
				}
				
				$SQLGroups .= $CGroup . $DimensionsSQL[$DimensionName]['GroupBy'];
				$C = ", ";
				$CGroup = ", ";
				
				if(count($OrderC) > 0){
					if(array_key_exists($No, $OrderC)){
						$OrderName = $DimensionsSQL[$DimensionName]['OrderVal'];
						$OrderParam .= $OrderComa . $OrderName . " " . $OrderC[$No];
						$OrderComa = ", ";
					}
				}
				
				$No++;
			}
		}
		//print_r($Dimensions);
		//exit(0);
		
		$SQLWhere = "";
		foreach($ToFilter as $KInclude => $KFilterVal){
			foreach($KFilterVal as $KFilter => $FilterVals){
				if(!in_array($KFilter, $Dimensions)){
					$SQLDimensions .= $C . $DimensionsSQL[$KFilter]['Name'];
					
					if(count($DimensionsSQL[$KFilter]['InnerJoin']) > 0){
						foreach($DimensionsSQL[$KFilter]['InnerJoin'] as $JoinTable => $JoinSQL){
							if(!in_array($JoinTable, $UsedInnerJ)) {
								$SQLInnerJoins .= $JoinSQL;
								$UsedInnerJ[] = $JoinTable;
							}
						}
					}
					//$SQLInnerJoins .= $DimensionsSQL[$KFilter]['InnerJoin'];
				}
				
				$ThereAreFilters = true;
				$KeySearch = $DimensionsSQL[$KFilter]['SearchName'];
				$SQLWhere .= " AND (";
				
				$And = ""; 
				$Or = "";
				if($KInclude == 'exclude'){
					foreach($FilterVals as $FVal){
						if($KFilter != 'sales_manager' && $KFilter != 'country' && $KFilter != 'dsp' && $KFilter != 'ssp' && $KFilter != 'type'){
							if(strpos($FVal, ' (') !== false){
								$arFv = explode('(', $FVal);
								$FVal = str_replace('*', '%', trim($arFv[0]));
							}
							
							$FVal = mysqli_real_escape_string($db->link, $FVal);
							
							$SQLWhere .= $And . $KeySearch . " NOT LIKE '$FVal'";
						}else{
							if($KFilter == 'type'){
								if($FVal == 'Deal'){
									$FVal = 1;
								}else{
									$FVal = 2;
								}
							}
							$SQLWhere .= $And . $KeySearch . " != '$FVal'";
						}
						$And = " AND "; 
					}
				}else{
					foreach($FilterVals as $FVal){
						if($KFilter != 'sales_manager' && $KFilter != 'country' && $KFilter != 'dsp' && $KFilter != 'ssp' && $KFilter != 'type'){
							if(strpos($FVal, ' (') !== false){
								$arFv = explode('(', $FVal);
								$FVal = str_replace('*', '%', trim($arFv[0]));
							}
							
							$FVal = mysqli_real_escape_string($db->link, $FVal);
							
							$SQLWhere .= $Or . $KeySearch . " LIKE '$FVal'";
						}else{
							if($KFilter == 'type'){
								if($FVal == 'Deal'){
									$FVal = 1;
								}else{
									$FVal = 2;
								}
							}
							$SQLWhere .= $Or . $KeySearch . " = '$FVal'";
						}
						$Or = " OR "; 
					}
					
				}
				$SQLWhere .= ") ";
			}
		}
	
		
		$SQLMetrics = "";
		$Bases = array();
		$SQLBases = "";
		foreach($Metrics as $MetricName){
			$MetricName = trim($MetricName);
			if($CSVResponse){
				$SQLMetrics .= $MetricsSQL[$MetricName]['SQLCSV'];
			}else{
				$SQLMetrics .= $MetricsSQL[$MetricName]['SQL'];
			}
			$Base = $MetricsSQL[$MetricName]['Base'];
			if(count($Base) > 0){
				foreach($Base as $B){
					if(!in_array($B, $Bases)){
						$Bases[] = $B;
						$SQLBases .= ", {ReportsTable}." . $B;
					}
				}
			}
			
			if(count($OrderC) > 0){
				if(array_key_exists($No, $OrderC)){
					$OrderName = $MetricsSQL[$MetricName]['OrderVal'];
					$OrderParam .= $OrderComa . $OrderName . " " . $OrderC[$No];
					$OrderComa = ", ";
				}
			}

			if(array_key_exists('InnerJoin', $MetricsSQL[$MetricName]) && count($MetricsSQL[$MetricName]['InnerJoin']) > 0){
				foreach($MetricsSQL[$MetricName]['InnerJoin'] as $JoinTable => $JoinSQL){
					if(!in_array($JoinTable, $UsedInnerJ)) {
						$SQLInnerJoins .= $JoinSQL;
						$UsedInnerJ[] = $JoinTable;
					}
				}
			}
			
			$No++;
		}
		
		
		
		//SI HAY FILTROS, CALCULA LOS TOTALES SIN FILRTOS
		$Nd = 0;
		if($ThereAreFilters){

			$SQLSuperQueryT = "SELECT '' $SQLMetrics FROM {ReportsTable} 
			INNER JOIN campaign ON campaign.id = {ReportsTable}.idCampaing 
			INNER JOIN agency ON campaign.agency_id = agency.id 
			INNER JOIN purchase_order ON campaign.purchase_order_id = purchase_order.id $SQLInnerJoinsTotals 
			WHERE {ReportsTable}.Date BETWEEN '$DFrom' AND '$DTo' $PubManFilter ";
			
			/*if(count($UnionTables) > 1){
				$Union = "";
				$SQLQueryT = "";
				if($Overall || 1==1){
					$SQLMetricsT = str_replace('{ReportsTable}', 'R', $SQLMetrics);
					$SQLQueryT = "SELECT '' $SQLMetricsT FROM (";
					foreach($UnionTables as $Table){
						$SQLBasesTo = str_replace('{ReportsTable}', $Table, $SQLBases);
						$SQLInnerJoinsTo = str_replace('{ReportsTable}', $Table, $SQLInnerJoinsTotals);
						$SQLQueryT .= "$Union (SELECT '' $SQLBasesTo FROM $Table INNER JOIN supplytag ON supplytag.id = $Table.idTag $SQLInnerJoinsTo WHERE $Table.Date BETWEEN '$DFrom' AND '$DTo' $PubManFilter) ";
						$Union = "UNION ALL";
					}    
					$SQLQueryT .= ")  AS R ";
					
				}else{
					foreach($UnionTables as $Table){
						$SQLQueryT .= "$Union (" . str_replace('{ReportsTable}', $Table, $SQLSuperQueryT) . ") ";
						$Union = "UNION ALL";
					}
				}
			}else{*/
			$SQLQueryT = str_replace('{ReportsTable}', $UnionTables[0], $SQLSuperQueryT);
			//}
			
			//echo $SQLQueryT;
			//exit(0);
			
			$Prefix = intval($mem_var->get('total_prefix'));
			$QueryKeyT = $Prefix . md5($SQLQueryT);
			$CachedTotalsNF = $mem_var->get($QueryKeyT);
			
			if($CachedTotalsNF === false || 1 == 1){
				
				if($IncludeTime){
					$DataT[$Nd][] = "";
				}
				
				$SuperQueryT = $db->query($SQLQueryT);
				while($Da = $db->fetch_array($SuperQueryT)){
					if($AddDimensions){
						foreach($Dimensions as $DimensionName){
							$DataT[$Nd][] = "";
						}
					}
					foreach($Metrics as $MetricName){
						$MetricName = trim($MetricName);
						
						if(is_array($Da)){
							if(array_key_exists($MetricsSQL[$MetricName]['Name'], $Da)){
							
								if($MetricsSQL[$MetricName]['NumberF']){
									//$DataT[$Nd][] = number_format($Da[$MetricsSQL[$MetricName]['Name']], 0, '', ',');
									$DataT[$Nd][] = $Da[$MetricsSQL[$MetricName]['Name']];
								}else{
									
									$DataT[$Nd][] = $Da[$MetricsSQL[$MetricName]['Name']];
								}
								
							}else{
								break;
							}
						}else{
							break;
						}
					}
					$Nd++;
				}
				
				if(array_key_exists($Nd, $DataT)){
					$CacheArrayT['DataNF'] = $DataT[$Nd];
				}
				
				$mem_var->set($QueryKeyT, $CacheArrayT, 30 * 60);
				$CachedTN = 0;
			}else{
				$CachedTN = 1;
				$DataT[$Nd] = $CachedTotalsNF['DataNF'];
			}
		}
		
		
		//CALCULA LOS TOTALES CON FILTROS
		$SQLSuperQueryT = "SELECT '' $SQLMetrics FROM {ReportsTable} 
		INNER JOIN campaign ON campaign.id = {ReportsTable}.idCampaing 
		INNER JOIN agency ON campaign.agency_id = agency.id 
		INNER JOIN purchase_order ON campaign.purchase_order_id = purchase_order.id $SQLInnerJoinsTotals 
		WHERE {ReportsTable}.Date BETWEEN '$DFrom' AND '$DTo' $SQLWhere $PubManFilter ";
		
		/*
		if(count($UnionTables) > 1){
			$Union = "";
			$SQLQueryT = "";

			$SQLMetricsT = str_replace('{ReportsTable}', 'R', $SQLMetrics);
			$SQLQueryT = "SELECT '' $SQLMetricsT FROM (";
			foreach($UnionTables as $Table){
				$SQLBasesTo = str_replace('{ReportsTable}', $Table, $SQLBases);
				$SQLInnerJoinsTo = str_replace('{ReportsTable}', $Table, $SQLInnerJoins);
				$SQLQueryT .= "$Union (SELECT '' $SQLBasesTo FROM $Table INNER JOIN campaign ON campaign.id = $Table.idCampaing $SQLInnerJoinsTo WHERE $Table.Date BETWEEN '$DFrom' AND '$DTo' $SQLWhere) ";
				$Union = "UNION ALL";
			}    
			$SQLQueryT .= ")  AS R ";
				
		}else{
		*/
		$SQLQueryT = str_replace('{ReportsTable}', $UnionTables[0], $SQLSuperQueryT);
		//}
		//echo $SQLQueryT;
		//exit(0);
		
		
		
		$Prefix = intval($mem_var->get('total_prefix'));
		$QueryKeyT = $Prefix . md5($SQLQueryT);
		$CachedTotals = $mem_var->get($QueryKeyT);
		
		if($CachedTotals === false || 1 == 1){
			
			if($IncludeTime){
				$DataT[$Nd][] = "";
			}
			
			
			$SuperQueryT = $db->query($SQLQueryT);
			if($db->num_rows($SuperQueryT) > 0){
				while($Da = $db->fetch_array($SuperQueryT)){
					if($AddDimensions){
						foreach($Dimensions as $DimensionName){
							$DataT[$Nd][] = "";
						}
					}
					foreach($Metrics as $MetricName){
						$MetricName = trim($MetricName);
						
						if(is_array($Da)){
							if(array_key_exists($MetricsSQL[$MetricName]['Name'], $Da)){
							
								if($MetricsSQL[$MetricName]['NumberF']){
									//$DataT[$Nd][] = number_format($Da[$MetricsSQL[$MetricName]['Name']], 0, '', ',');
									$DataT[$Nd][] = $Da[$MetricsSQL[$MetricName]['Name']];
								}else{
									
									$DataT[$Nd][] = $Da[$MetricsSQL[$MetricName]['Name']];
								}
								
							}else{
								break;
							}
						}else{
							break;
						}
					}
					$Nd++;
				}
			}
			
			$CacheArrayT['Data'] = $DataT;
			
			$mem_var->set($QueryKeyT, $CacheArrayT, 30 * 60);
			$CachedT = 0;
		}else{
			$CachedT = 1;
			$DataT = $CachedTotals['Data'];
		}
			
		$Nd = 0;
		//CALCULA EL RESTO DE LA TABLA
		$SQLSuperQuery = "SELECT SQL_CALC_FOUND_ROWS $SQLDimensions $SQLMetrics , reports.SSP AS idSSP FROM {ReportsTable} INNER JOIN campaign ON campaign.id = {ReportsTable}.idCampaing INNER JOIN agency ON campaign.agency_id = agency.id $SQLInnerJoins WHERE {ReportsTable}.Date BETWEEN '$DFrom' AND '$DTo' $SQLWhere $PubManFilter $SQLGroups";
		/*
		if(count($UnionTables) > 1){
			$Union = "";
			$SQLQuery = "";
			if($Overall){
				$SQLMetrics = str_replace('{ReportsTable}', 'R', $SQLMetrics);
				$SQLQuery = "SELECT SQL_CALC_FOUND_ROWS $SQLDimensionsOverall $SQLMetrics FROM (";
				foreach($UnionTables as $Table){
					$SQLBasesTo = str_replace('{ReportsTable}', $Table, $SQLBases);
					$SQLInnerJoinsTo = str_replace('{ReportsTable}', $Table, $SQLInnerJoins);
					$SQLQuery .= "$Union (SELECT $SQLDimensions $SQLBasesTo FROM $Table INNER JOIN campaign ON campaign.id = {ReportsTable}.idCampaing $SQLInnerJoinsTo WHERE $Table.Date BETWEEN '$DFrom' AND '$DTo' $SQLWhere) ";
					$Union = "UNION ALL";
				}    
				$SQLQuery .= ")  AS R $SQLGroups";
				
			}else{
				foreach($UnionTables as $Table){
					if($Union != ""){
						$SQLSuperQuery = str_replace('SQL_CALC_FOUND_ROWS', '', $SQLSuperQuery);
					}
					$SQLQuery .= "$Union (" . str_replace('{ReportsTable}', $Table, $SQLSuperQuery) . ") ";
					$Union = "UNION ALL";
				}
			}
		}else{
		*/
			$SQLQuery = str_replace('{ReportsTable}', $UnionTables[0], $SQLSuperQuery);
		//}
		
		if($OrderParam != ""){
			$SQLQuery .= " ORDER BY $OrderParam";
			if($Start !== false || $Length !== false){
				$SQLQuery .= " LIMIT $Start, $Length";
			}
		}
		//echo $SQLQuery;
		//exit(0);
		$QueryKey = md5($SQLQuery);
		$CachedReport = $mem_var->get('report_prefix_' . $QueryKey);
		
		if($CachedReport === false || 1==1){
			$Cached = 0;
			$SuperQuery = $db->query($SQLQuery);
			
			$sqlCount = 'SELECT FOUND_ROWS();';
			$CntTotal = $db->getOne($sqlCount);
			$TDim = 0;
			
			if($db->num_rows($SuperQuery) > 0){
				while($Da = $db->fetch_array($SuperQuery)){
					if($IncludeTime){
						$Data[$Nd][] = $Da[$TimeName];
						$TDim++;
					}
					if($AddDimensions){
						foreach($Dimensions as $DimensionName){
							
							$DimensionValue = $Da[$DimensionsSQL[$DimensionName]['OrderVal']];
							if($DimensionValue != ''){
								if($DimensionName == 'campaign_name'){
									$arSS = explode('--',$DimensionValue);
									$Data[$Nd][] = $arSS[0];
								}else{
									$Data[$Nd][] = $DimensionValue;
								}
							}else{
								$Data[$Nd][] = 'Unknown';
							}
							$TDim++;
						}
					}
					foreach($Metrics as $MetricName){
						$MetricName = trim($MetricName);
						$MetricSName = $MetricsSQL[$MetricName]['Name'];
						if($MetricsSQL[$MetricName]['NumberF']){
							//$Data[$Nd][] = number_format($Da[$MetricSName], 0, '', ',');
							$Data[$Nd][] = $Da[$MetricSName];
						}else{
							if(($MetricSName == 'FIRST' || $MetricSName == 'MID' || $MetricSName == 'THIRD' || $MetricSName == 'Complete25' || $MetricSName == 'Complete50' || $MetricSName == 'Complete75') && $Da[$MetricSName] === NULL){
								$Data[$Nd][] = "-";
							}elseif(($MetricSName == 'CTR' || $MetricSName == 'VTR' || $MetricSName == 'FIRST' || $MetricSName == 'MID' || $MetricSName == 'THIRD' || $MetricSName == 'RebatePercent' || $MetricSName == 'ViewabilityPercent') && floatval(str_replace('%','',$Da[$MetricSName])) == 0 && $CSVResponse === true){
								$Data[$Nd][] = '0.00%';
							}elseif(($MetricSName == 'CPM' || $MetricSName == 'Rebate' || $MetricSName == 'Revenue' || $MetricSName == 'NetRevenue') && floatval(str_replace('$','',$Da[$MetricSName])) == 0 && $CSVResponse === true){
								$Data[$Nd][] = '$0.00';
							}else{
								$Data[$Nd][] = $Da[$MetricSName];
							}
						}
					}
					$Nd++;
				}
			}
			
			$CacheArray['Data'] = $Data;
			$CacheArray['Total'] = $CntTotal;
			
			$mem_var->set('report_prefix_' . $QueryKey, $CacheArray, 30 * 60);
		}else{
			$Cached = 1;
			$Data = $CachedReport['Data'];
			$CntTotal = $CachedReport['Total'];
		}
		//$Data[0]
	}else{
		header('HTTP/1.0 404');
		exit(0);
	}
	mysqli_close ( $db->link );

	if(isset($_POST['csv'])){
		if($_POST['csv'] == 'true'){
			header("Content-type: text/csv");
			
			$ComaH = "";
			
			if($IncludeTime){
				echo "Time";
				$ComaH = ",";
			}
			
			if($AddDimensions){
				foreach($Dimensions as $DimensionName){
					echo $ComaH . $DimensionsSQL[$DimensionName]['HeadName'];
					$ComaH = ",";
				}
			}
			
			foreach($Metrics as $MetricName){
				echo $ComaH . $MetricsSQL[$MetricName]['HeadName'];
				$ComaH = ",";
			}
			
			echo "\n";
			
			foreach($Data as $Dcv){
				$Coma = '';
				foreach($Dcv as $V){
					echo $Coma . '"' . $V . '"';
					$Coma = ',';
				}
				echo "\n";
			}
			
			exit(0);
		}
	}
?>{
  "draw": <?php if(isset($_POST['draw'])){ echo intval($_POST['draw']); } else { echo "0"; } ?>,
  "recordsTotal": <?php echo $CntTotal; ?>,
  "recordsFiltered": <?php echo $CntTotal; ?>,
  "data": <?php echo safe_json_encode($Data); ?>,
  "dataT": <?php echo json_encode($DataT); ?>,
  "SQL": "<?php //echo $SQLQuery; ?>"
}