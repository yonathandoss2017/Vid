<?php
	
	function updateReportCards($db2, $Date){
		global $db;
		
		$sql = "SELECT * FROM user";
		$query = $db2->query($sql);
		if($db2->num_rows($query) > 0){
			while($User = $db2->fetch_array($query)){
				$Roles = json_decode($User['roles']);
				if(in_array('ROLE_ADMIN', $Roles)){
					updateUserCards($db2, $User['id'], $Date, true);
					//echo "Update Admin " . $User['id'] . "\n";
				}else{
					updateUserCards($db2, $User['id'], $Date, false);	
					//echo "Update Sales " . $User['id'] . "\n";
				}
				
			}	
		}	
	}
	
	function updateUserCards($db2, $idUser, $Date, $Admin = false){
		global $db;
		
		$DateTime = new DateTime($Date);
		$FirstDay = $DateTime->format('Y-m-') . '01';
		$LastDate = $DateTime->format('Y-m-t');
		$Month = $DateTime->format('n');
		$Year = $DateTime->format('Y');
		
		if($Admin){
			$sql = "SELECT SUM(Revenue) FROM reports
			WHERE reports.Date BETWEEN '$FirstDay' AND '$LastDate'";
		}else{
			$sql = "SELECT SUM(Revenue) FROM reports
			INNER JOIN campaign ON campaign.id = reports.idCampaing
			INNER JOIN agency ON campaign.agency_id = agency.id 
			WHERE agency.sales_manager_id = '$idUser' AND reports.Date BETWEEN '$FirstDay' AND '$LastDate'";
		}
		$Revenue = $db->getOne($sql);
		
		if($Admin){
			$sql = "SELECT SUM(Impressions) FROM reports
			WHERE reports.Date = '$Date'";
		}else{
			$sql = "SELECT SUM(Impressions) FROM reports
			INNER JOIN campaign ON campaign.id = reports.idCampaing
			INNER JOIN agency ON campaign.agency_id = agency.id 
			WHERE agency.sales_manager_id = '$idUser' AND reports.Date = '$Date'";
		}
		$Impressions = $db->getOne($sql);
		
		if($Admin){
			$sql = "SELECT COUNT(DISTINCT(reports.idCampaing)) FROM reports
			INNER JOIN campaign ON campaign.id = reports.idCampaing
			WHERE reports.Date = '$Date' AND reports.Impressions > 0 AND campaign.type = 1";
		}else{
			$sql = "SELECT COUNT(DISTINCT(idCampaing)) FROM reports
			INNER JOIN campaign ON campaign.id = reports.idCampaing
			INNER JOIN agency ON campaign.agency_id = agency.id 
			WHERE agency.sales_manager_id = '$idUser' AND campaign.type = 1 AND reports.Date = '$Date' AND reports.Impressions > 0";
		}
		$ActiveDeals = $db->getOne($sql);
		
		
		if($Admin){
			$sql = "SELECT COUNT(DISTINCT(reports.idCampaing)) FROM reports
			INNER JOIN campaign ON campaign.id = reports.idCampaing
			WHERE reports.Date = '$Date' AND reports.Impressions > 0 AND campaign.type = 2";
		}else{
			$sql = "SELECT COUNT(DISTINCT(idCampaing)) FROM reports
			INNER JOIN campaign ON campaign.id = reports.idCampaing
			INNER JOIN agency ON campaign.agency_id = agency.id 
			WHERE agency.sales_manager_id = '$idUser' AND campaign.type = 2 AND reports.Date = '$Date' AND reports.Impressions > 0";
		}
		$ActiveCamp = $db->getOne($sql);
		
		/*
		if($Admin){
			$sql = "SELECT ROUND((SUM(VImpressions) / SUM(Impressions) * 100), 2) FROM reports
			WHERE reports.Date = '$Date'";
		}else{
			$sql = "SELECT ROUND((SUM(VImpressions) / SUM(Impressions) * 100), 2) FROM reports
			INNER JOIN campaign ON campaign.id = reports.idCampaing
			INNER JOIN agency ON campaign.agency_id = agency.id 
			WHERE agency.sales_manager_id = '$idUser' AND reports.Date = '$Date'";
		}
		$Viewability = $db->getOne($sql);
		*/
		
		if($Admin){
			$Objective = 0;
		}else{
			$sql = "SELECT goal FROM target WHERE user_id = '$idUser' AND month = '$Month' AND year = '$Year' LIMIT 1";
			$Objective = intval($db2->getOne($sql));
		}
		
		$sql = "SELECT id FROM reports_cards WHERE user_id = $idUser LIMIT 1";
		$idO = intval($db2->getOne($sql));
		if($idO > 0){
			$sql = "UPDATE reports_cards SET objective = '$Objective', impressions = '$Impressions', active_deals = '$ActiveDeals', active_direct_campaigns = '$ActiveCamp', revenue = '$Revenue' WHERE id = '$idO' LIMIT 1";
		}else{
			$sql = "INSERT INTO reports_cards (user_id, objective, impressions, active_deals, active_direct_campaigns, revenue) VALUES ('$idUser', '$Objective', '$Impressions', '$ActiveDeals', '$ActiveCamp', '$Revenue')";
		}
		
		$db2->query($sql);
		
		if(!$Admin){
			$sql = "UPDATE target SET reached = '$Revenue' WHERE user_id = $idUser AND month = $Month AND year = $Year LIMIT 1";
			$db2->query($sql);
		}
	}