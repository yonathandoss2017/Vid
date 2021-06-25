<?php
	
	function updateReportCards($db2, $Date){
		global $db;
		
		$sql = "SELECT * FROM user";
		$query = $db2->query($sql);
		if($db2->num_rows($query) > 0){
			while($User = $db2->fetch_array($query)){
				$Roles = json_decode($User['roles']);
				if(in_array('ROLE_ADMIN', $Roles)){
					updateUserCards($db2, $User['id'], $Date, $Roles, true);
					//echo "Update Admin " . $User['id'] . "\n";
				}else{
					updateUserCards($db2, $User['id'], $Date, $Roles, false);
					//echo "Update Sales " . $User['id'] . "\n";
				}
				
			}	
		}	
	}

	/**
	 * Gets the string with subordinates user for the head
	 *
	 * @param string $idUser
	 * @param SQL $db2
	 *
	 * @return string subordinates user string
	 */
	function getUsersQueryForManagerHeads(string $idUser, SQL $db2): string {
		$PubManFilter = " AND (agency.sales_manager_id = '$idUser'";
		$sqlUsers = "SELECT id FROM user WHERE manager_id = '$idUser'";
		$queryS = $db2->query($sqlUsers);
		if($db2->num_rows($queryS) > 0){
			while($U = $db2->fetch_array($queryS)){
				$idS = $U['id'];
				$PubManFilter .= " OR agency.sales_manager_id = '$idS' ";
			}
		}
		$PubManFilter .= ")";

		return $PubManFilter;
	}

	/**
	 * Gets the string with suboordinates user for the vp
	 *
	 * @param string $idUser
	 * @param SQL $db2
	 *
	 * @return string subordinates user string
	 */
	function getUsersQueryForCountryManager(string $idUser, SQL $db2): string {
		$PubManFilter = " AND (agency.sales_manager_id = '$idUser'";
		$sqlUsers = "SELECT user.id FROM user INNER JOIN user AS manager ON user.manager_id = manager.id WHERE user.manager_id = '$idUser' OR manager.manager_id = '$idUser'";
		$queryS = $db2->query($sqlUsers);
		if($db2->num_rows($queryS) > 0){
			while($U = $db2->fetch_array($queryS)){
				$idS = $U['id'];
				$PubManFilter .= " OR agency.sales_manager_id = '$idS' ";
			}
		}
		$PubManFilter .= ")";

		return $PubManFilter;
	}

	/**
	 * Gets the string with suboordinates user for the vp
	 *
	 * @param string $idUser
	 * @param SQL $db2
	 *
	 * @return string subordinates user string
	 */
	function getUsersQueryForVp(string $idUser, SQL $db2): string {
		$PubManFilter = " AND (agency.sales_manager_id = '$idUser'";
		$sqlUsers = "SELECT user.id FROM user INNER JOIN user AS manager ON user.manager_id = manager.id LEFT JOIN user AS manager_head ON manager.manager_id = manager_head.id WHERE user.manager_id = '$idUser' OR manager.manager_id = '$idUser' OR manager_head.manager_id = '$idUser'";
		$queryS = $db2->query($sqlUsers);
		if($db2->num_rows($queryS) > 0){
			while($U = $db2->fetch_array($queryS)){
				$idS = $U['id'];
				$PubManFilter .= " OR agency.sales_manager_id = '$idS' ";
			}
		}
		$PubManFilter .= ")";

		return $PubManFilter;
	}
	
	function updateUserCards($db2, $idUser, $Date, $Roles, $Admin = false){
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
            if (in_array("ROLE_SALES_MANAGER_HEAD", $Roles)) {
                $sql = "SELECT SUM(Revenue) FROM reports
				INNER JOIN campaign ON campaign.id = reports.idCampaing
				INNER JOIN agency ON campaign.agency_id = agency.id
				WHERE reports.Date BETWEEN '$FirstDay' AND '$LastDate'";

				$sql = $sql . getUsersQueryForManagerHeads($idUser, $db2);
			} elseif (in_array("ROLE_COUNTRY_MANAGER", $Roles)) {
				$sql = "SELECT SUM(Revenue) FROM reports
				INNER JOIN campaign ON campaign.id = reports.idCampaing
				INNER JOIN agency ON campaign.agency_id = agency.id
				WHERE reports.Date BETWEEN '$FirstDay' AND '$LastDate'";

				$sql = $sql . getUsersQueryForCountryManager($idUser, $db2);
            } elseif (in_array("ROLE_SALES_VP", $Roles)) {
				$sql = "SELECT SUM(Revenue) FROM reports
				INNER JOIN campaign ON campaign.id = reports.idCampaing
				INNER JOIN agency ON campaign.agency_id = agency.id
				WHERE reports.Date BETWEEN '$FirstDay' AND '$LastDate'";

				$sql = $sql . getUsersQueryForVp($idUser, $db2);
			} else {
				$sql = "SELECT SUM(Revenue) FROM reports
				INNER JOIN campaign ON campaign.id = reports.idCampaing
				INNER JOIN agency ON campaign.agency_id = agency.id
				WHERE agency.sales_manager_id = '$idUser' AND reports.Date BETWEEN '$FirstDay' AND '$LastDate'";
			}
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
			if (in_array("ROLE_SALES_MANAGER_HEAD", $Roles)) {
                $sql = "SELECT COUNT(DISTINCT(idCampaing)) FROM reports
				INNER JOIN campaign ON campaign.id = reports.idCampaing
				INNER JOIN agency ON campaign.agency_id = agency.id
				WHERE campaign.type = 1 AND reports.Date = '$Date' AND reports.Impressions > 0";

				$sql = $sql . getUsersQueryForManagerHeads($idUser, $db2);
			} elseif (in_array("ROLE_COUNTRY_MANAGER", $Roles)) {
				$sql = "SELECT COUNT(DISTINCT(idCampaing)) FROM reports
				INNER JOIN campaign ON campaign.id = reports.idCampaing
				INNER JOIN agency ON campaign.agency_id = agency.id
				WHERE campaign.type = 1 AND reports.Date = '$Date' AND reports.Impressions > 0";

				$sql = $sql . getUsersQueryForCountryManager($idUser, $db2);
            } elseif (in_array("ROLE_SALES_VP", $Roles)) {
				$sql = "SELECT COUNT(DISTINCT(idCampaing)) FROM reports
				INNER JOIN campaign ON campaign.id = reports.idCampaing
				INNER JOIN agency ON campaign.agency_id = agency.id
				WHERE campaign.type = 1 AND reports.Date = '$Date' AND reports.Impressions > 0";

				$sql = $sql . getUsersQueryForVp($idUser, $db2);
			} else {
				$sql = "SELECT COUNT(DISTINCT(idCampaing)) FROM reports
				INNER JOIN campaign ON campaign.id = reports.idCampaing
				INNER JOIN agency ON campaign.agency_id = agency.id
				WHERE agency.sales_manager_id = '$idUser' AND campaign.type = 1 AND reports.Date = '$Date' AND reports.Impressions > 0";
			}
		}
		$ActiveDeals = $db->getOne($sql);
		
		
		if($Admin){
			$sql = "SELECT COUNT(DISTINCT(reports.idCampaing)) FROM reports
			INNER JOIN campaign ON campaign.id = reports.idCampaing
			WHERE reports.Date = '$Date' AND reports.Impressions > 0 AND campaign.type = 2";
		}else{
			if (in_array("ROLE_SALES_MANAGER_HEAD", $Roles)) {
                $sql = "SELECT COUNT(DISTINCT(idCampaing)) FROM reports
				INNER JOIN campaign ON campaign.id = reports.idCampaing
				INNER JOIN agency ON campaign.agency_id = agency.id
				WHERE campaign.type = 2 AND reports.Date = '$Date' AND reports.Impressions > 0";

				$sql = $sql . getUsersQueryForManagerHeads($idUser, $db2);
			} elseif (in_array("ROLE_COUNTRY_MANAGER", $Roles)) {
				$sql = "SELECT COUNT(DISTINCT(idCampaing)) FROM reports
				INNER JOIN campaign ON campaign.id = reports.idCampaing
				INNER JOIN agency ON campaign.agency_id = agency.id
				WHERE campaign.type = 2 AND reports.Date = '$Date' AND reports.Impressions > 0";

				$sql = $sql . getUsersQueryForCountryManager($idUser, $db2);
            } elseif (in_array("ROLE_SALES_VP", $Roles)) {
				$sql = "SELECT COUNT(DISTINCT(idCampaing)) FROM reports
				INNER JOIN campaign ON campaign.id = reports.idCampaing
				INNER JOIN agency ON campaign.agency_id = agency.id
				WHERE campaign.type = 2 AND reports.Date = '$Date' AND reports.Impressions > 0";

				$sql = $sql . getUsersQueryForVp($idUser, $db2);
			} else {
				$sql = "SELECT COUNT(DISTINCT(idCampaing)) FROM reports
				INNER JOIN campaign ON campaign.id = reports.idCampaing
				INNER JOIN agency ON campaign.agency_id = agency.id
				WHERE agency.sales_manager_id = '$idUser' AND campaign.type = 2 AND reports.Date = '$Date' AND reports.Impressions > 0";
			}
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