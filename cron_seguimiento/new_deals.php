<?php	
	exit(0);
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	define('AAL_DEAL_ID', '1053459');
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/common.lib.php');
	
	require '/var/www/html/site/include/PHPMailer/PHPMailerAutoload.php';
		
	$db = new SQL($dbhost, 'vidoomy_adv', $dbuser, $dbpass);
	
	$dbuser3 = "root";
	$dbpass3 = "pthFTa8Lp25xs7Frkqgkz5HRebmwVGPY";
	//$dbhost3 = "aa4mgb1tsk2y6v.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbhost3 = "aa14extn6ty9ilx.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname3 = "vidoomy-advertisers-panel";
	$db3 = new SQL($dbhost3, $dbname3, $dbuser3, $dbpass3);
	
	$date1 = new DateTime();
	$date1->add(DateInterval::createFromDateString('today'));
	$Today = $date1->format('Y-m-d');
	//$Today = '2020-05-04';
	
	$sql = "SELECT DISTINCT(idCampaing) AS idCampaing FROM reports 
	INNER JOIN campaign ON campaign.id = reports.idCampaing
	WHERE reports.Date = '$Today' AND reports.Impressions > 0";
	
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($TodayDeals = $db->fetch_array($query)){
			$idCampaing = $TodayDeals['idCampaing'];
			//echo $idCampaing;
			
			$sql = "SELECT COUNT(*) FROM sent_activation WHERE idCampaing = $idCampaing";
			if(intval($db->getOne($sql)) == 0){
			
				$sql = "SELECT COUNT(*) FROM reports WHERE Date < '$Today' AND Impressions > 0 AND idCampaing = $idCampaing";
				if($db->getOne($sql) == 0){
					$sql = "INSERT INTO sent_activation (idCampaing, Date) VALUES ($idCampaing, '$Today')";
					$db->query($sql);
					
					$sql = "SELECT * FROM campaign WHERE id = $idCampaing LIMIT 1";
					$queryCamp = $db3->query($sql);
					$CampData = $db3->fetch_array($queryCamp);
					
					$agencyId = $CampData['agency_id'];
					
					$sql = "SELECT CONCAT(user.name, ' ', user.last_name) AS Name, user.email AS Email, user.manager_id AS HeadID FROM agency INNER JOIN user ON user.id = agency.sales_manager_id WHERE agency.id = $agencyId";
					
					$querySales = $db3->query($sql);
					$SalesData = $db3->fetch_array($querySales);
					
					$NameSalesManager = $SalesData['Name'];
					$EmailSalesManager = $SalesData['Email'];
					$HeadId = intval($SalesData['HeadID']);
					
					$DealId = $CampData['deal_id'];
					
					if(strpos($DealId, '(') !== false && strpos($DealId, ')') !== false){
						$dEx = explode('(', $DealId);
						$DealId = $dEx[0];
					}
					
					$DealName = $CampData['name'];
					
					$OriginalTpl = file_get_contents('/var/www/html/login/emailstpl/new_deal.html');
					$EmailText = str_replace('#SalesManager#', $NameSalesManager, $OriginalTpl);
					if($CampData['type'] == 2){
						$EmailText = str_replace('#DealCamp#', 'la Campaña', $EmailText);
						$Art = 'La campaña';
					}else{
						$EmailText = str_replace('#DealCamp#', 'el Deal', $EmailText);
						$Art = 'El deal';
					}
					$EmailText = str_replace('#CampName#', $DealName, $EmailText);
					$EmailText = str_replace('#DealID#', $DealId, $EmailText);
					
					$EmailTitle = "$Art $DealName ha empezado a comprar";
					
					$mail = new PHPMailer;
					$mail->isSMTP();
					$mail->SMTPDebug = 0;
					$mail->Debugoutput = 'html';
					
					$mail->Host = 'smtp.gmail.com';
					$mail->Port = 465;
					$mail->SMTPSecure = 'ssl';
					$mail->SMTPAuth = true;
					$mail->Username = "notifysystem@vidoomy.net";
					$mail->Password = "NoTyFUCK05-1";
					$mail->CharSet = 'UTF-8';
					$mail->setFrom('notify@vidoomy.net', 'Vidoomy');
					$mail->addReplyTo('notify@vidoomy.net', 'Vidoomy');
					
					//$EmailSalesManager = 'federicoizuel@gmail.com';
					$mail->addAddress($EmailSalesManager, $NameSalesManager);
					
					
					//$mail->AddBCC('federico.izuel@vidoomy.com');
					//$mail->AddBCC('marcos.cuesta@vidoomy.com');
					//$mail->AddBCC('eric.raventos@vidoomy.com');
					
					
					if(intval($HeadId) > 0){
						$sql = "SELECT email FROM user WHERE id = $HeadId LIMIT 1";
						$EmailHead = $db3->getOne($sql);
						
						$sql = "SELECT CONCAT(name, ' ', last_name) FROM user WHERE id = $HeadId LIMIT 1";
						$NameHead = $db3->getOne($sql);
						
						//echo "Head Name: $NameHead \n";
						//echo "Head Email: $EmailHead \n";
						//$mail->AddBCC($EmailHead);
					}
					
					$mail->Subject = $EmailTitle;// . " (Fe de erratas)"
					$mail->msgHTML($EmailText);
					$mail->send();
					
					$mail = new PHPMailer;
					$mail->isSMTP();
					$mail->SMTPDebug = 0;
					$mail->Debugoutput = 'html';
					
					$mail->Host = 'smtp.gmail.com';
					$mail->Port = 465;
					$mail->SMTPSecure = 'ssl';
					$mail->SMTPAuth = true;
					$mail->Username = "notifysystem@vidoomy.net";
					$mail->Password = "NoTyFUCK05-1";
					$mail->CharSet = 'UTF-8';
					$mail->setFrom('notify@vidoomy.net', 'Vidoomy');
					$mail->addReplyTo('notify@vidoomy.net', 'Vidoomy');
					
					$mail->addAddress('federico.izuel@vidoomy.com', 'Antonio Simarro');
					//$mail->addAddress('antonio.simarro@vidoomy.com', 'Antonio Simarro');

					if (AAL_DEAL_ID == $DealId) {
						$mail->AddBCC('patricia.palmero@vidoomy.com', 'Patricia Palmero');
						$mail->AddBCC('ernesto.gonzalez@vidoomy.com', 'Ernesto Gonzalez');
					}
					
					$mail->Subject = $EmailTitle;
					$mail->msgHTML(str_replace($NameSalesManager , 'Tony', $EmailText));
					$mail->send();

					
				}
			}
		}
	}