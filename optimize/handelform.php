<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 1);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('../config.php');
	require('../constantes.php');
	require('../db.php');
	require('../common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	header("Allow: GET, POST, OPTIONS, PUT, DELETE");
	$method = $_SERVER['REQUEST_METHOD'];
	if($method == "OPTIONS") {
	    die();
	}
	
	if(isset($_POST['formtype'])){
		if($_POST['formtype'] == 1){
			$Name = $_POST['groupname'];
			$TagId = intval($_POST['grouptagid']);
			$Country = intval($_POST['country']);
			$Description = $Name . '_OPEN';
			
			if($Country == 0){
				$Country = 246;
			}
			
			$sql = "INSERT INTO demandgroup (Name, Country, Added, Active) VALUES ('$Name', '$Country', CURRENT_TIMESTAMP, 1)";
			$db->query($sql);
			
			$idGroup = mysqli_insert_id($db->link);
			
			$sql = "INSERT INTO demandtags (idGroup, Open, DemandTagID, Description, DomainListId, MinFill, MinRequests) VALUES ('$idGroup', '1', '$TagId', '$Description', '0', '0', '0')";
			$db->query($sql);
			
			echo $idGroup;
		}elseif($_POST['formtype'] == 2){
			$idGroup = $_POST['idgroup'];
			$Description = $_POST['description'];
			$TagId = $_POST['tagid'];
			$Fill = $_POST['fill'];
			$Requests = $_POST['requests'];
			$DListId = $_POST['listid'];
			
			$sql = "INSERT INTO demandtags (idGroup, Open, DemandTagID, Description, DomainListId, MinFill, MinRequests) VALUES ('$idGroup', '0', '$TagId', '$Description', '$DListId', '$Fill', '$Requests')";
			$db->query($sql);
		}elseif($_POST['formtype'] == 3){
			$idTag = intval($_POST['idtag']);
			$OTag = intval($_POST['otag']);
			$KPI = $_POST['kpi'];
			$Type = intval($_POST['criteria']);
			
			if($idTag > 0 && $OTag > 0 && $KPI > 0){
				$sql = "INSERT INTO demandtagrules (idTag, idTagO, Type, KPI) VALUES ('$idTag', '$OTag', '$Type', '$KPI')";
				$db->query($sql);
			}
		}elseif($_POST['formtype'] == 4){
			$Description = $_POST['description'];
			$TagId = $_POST['tagid'];
			$Fill = $_POST['fill'];
			$Requests = $_POST['requests'];
			$DListId = $_POST['listid'];
			$idTag = $_POST['idtag'];
			
			$sql = "UPDATE demandtags SET DemandTagID = '$TagId', Description = '$Description', DomainListId = '$DListId', MinFill = '$Fill', MinRequests = '$Requests' WHERE id = '$idTag' LIMIT 1";
			$db->query($sql);
		}elseif($_POST['formtype'] == 5){
			$Description = $_POST['description'];
			$TagId = $_POST['tagid'];
			$idTag = $_POST['idtag'];
			
			$sql = "UPDATE demandtags SET DemandTagID = '$TagId', Description = '$Description' WHERE id = '$idTag' LIMIT 1";
			$db->query($sql);
		}
	}