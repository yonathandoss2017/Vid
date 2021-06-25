<?php
	
Class PM {
	
	function getNewDomains($idAccM, $count = true, $MonthYear = false){
		global $db;
		
		$NewDomains = '0';
		$sql = "SELECT id FROM " . USERS . " WHERE AccM = '$idAccM'";
		$query = $db->query($sql);
		if($db->num_rows($query) > 0){
			//echo 'AB';
			$idstring = '';
			$or = '';
			while($Pub = $db->fetch_array($query)){
				$idPub = $Pub['id'];
				$idstring .= "$or idUser = '$idPub'";
				$or = " OR";
			}
			
			if($MonthYear){
				$FirstDay = $MonthYear . "-01";
				$LastDay = date("Y-m-t", strtotime($FirstDay));
			}else{
				$FirstDay = date("Y-m-") . '01';
				$LastDay = date("Y-m-t");
			}
			if($count){
				$sql = "SELECT COUNT(*) FROM " . SITES . " WHERE ($idstring) AND date BETWEEN '$FirstDay' AND '$LastDay'";
				$NewDomains = $db->getOne($sql);
			}else{
				$sql = "SELECT id FROM " . SITES . " WHERE ($idstring) AND date BETWEEN '$FirstDay' AND '$LastDay' ORDER BY id DESC";
				$query2 = $db->query($sql);
				if($db->num_rows($query2) > 0){
					$ArrayDomIds = array();
					while($Dom = $db->fetch_array($query2)){
						$ArrayDomIds[] = $Dom['id'];
					}
					$NewDomains = $ArrayDomIds;
				}
				
			}
		}
		
		return $NewDomains;	
	}
	
	function getNewPublishers($idAccM, $count = true, $MonthYear = false){
		global $db;
		
		$NewPubs = '0';
			
		if($MonthYear){
			$FirstDay = $MonthYear . "-01";
			$LastDay = date("Y-m-t", strtotime($FirstDay));
		}else{
			$FirstDay = date("Y-m-") . '01';
			$LastDay = date("Y-m-t");
		}
		
		if($count){
			$sql = "SELECT COUNT(*) FROM " . USERS . " WHERE AccM = '$idAccM' AND date BETWEEN '$FirstDay' AND '$LastDay'";
			$NewPubs = $db->getOne($sql);
		}else{
			$sql = "SELECT id FROM " . USERS . " WHERE AccM = '$idAccM' AND date BETWEEN '$FirstDay' AND '$LastDay' ORDER BY id DESC";
			$query = $db->query($sql);
			if($db->num_rows($query) > 0){
				$ArrayPubIds = array();
				while($Pub = $db->fetch_array($query)){
					$ArrayPubIds[] = $Pub['id'];
				}
				$NewPubs = $ArrayPubIds;
			}
		}
		
		return $NewPubs;	
	}
	
	function getDomainFormatLoads($idSite, $Date = ''){
		global $db;
		
		if($Date == ''){
			$Date = date('Y-m-d');
		}
		$sql = "SELECT SUM(formatLoads) FROM " . STATS . " WHERE idSite = '$idSite' AND Date = '$Date'";
		return $db->getOne($sql);
	}
	
	function getDomainFormatLoadsPerDay($idSite, $MonthYear){
		global $db;
		$ArrayDates = false;
		
		$FirstDay = $MonthYear . "-01";
		$LastDay = date("Y-m-t", strtotime($FirstDay));
		
		$sql = "SELECT Date, SUM(formatLoads) AS FL FROM stats WHERE idSite = '$idSite' AND Date BETWEEN '$FirstDay' AND '$LastDay' GROUP BY Date ORDER BY Date ASC";
		$query = $db->query($sql);
		if($db->num_rows($query) > 0){
			$ArrayDates = array();
			while($Dom = $db->fetch_array($query)){
				$ArrayDates[$Dom['Date']] = $Dom['FL'];
			}
		}
		
		return $ArrayDates;
	}
	
	function getPublisherFormatLoadsPerDay($idUser, $MonthYear){
		global $db;
		$ArrayDates = false;
		
		$FirstDay = $MonthYear . "-01";
		$LastDay = date("Y-m-t", strtotime($FirstDay));
		
		$sql = "SELECT Date, SUM(formatLoads) AS FL FROM stats WHERE idUser = '$idUser' AND Date BETWEEN '$FirstDay' AND '$LastDay' GROUP BY Date ORDER BY Date ASC";
		$query = $db->query($sql);
		if($db->num_rows($query) > 0){
			$ArrayDates = array();
			while($Dom = $db->fetch_array($query)){
				$ArrayDates[$Dom['Date']] = $Dom['FL'];
			}
		}
		
		return $ArrayDates;
	}
	
	function getPublisherFormatLoads($idPub, $Date = ''){
		global $db;
		
		if($Date == ''){
			$Date = date('Y-m-d');
		}
		$sql = "SELECT SUM(formatLoads) FROM " . STATS . " WHERE idUser = '$idPub' AND Date = '$Date'";
		return $db->getOne($sql);
	}
	
	/*
	function getMonthDomains($idAccM, $MonthYear = false, $FLLimit = 20000){
		global $db;
		
		$LostDomains = array();
		$PremiumDomains = array();
		
		$ArrayDomains = $this->getNewDomains($idAccM, false, $MonthYear);
		//print_r($ArrayDomains);
		
		if($ArrayDomains != 0){
			
			foreach($ArrayDomains as $Dom){
				$isPremium = false;
				$isLost = false;
				$OneDay = false;
				
				$DatesArray = $this->getDomainFormatLoadsPerDay($Dom, $MonthYear);
				//print_r($DatesArray);
				
				foreach($DatesArray as $Date => $FL){
					
					if($FL >= $FLLimit){
						$isPremium = true;
						$OneDay = false;
						$isLost = false;
					}
					
					if($isPremium){
						if($FL < $FLLimit){
							if($OneDay){
								$isPremium = false;
								$isLost = true;
							}
							$OneDay = true;
						}
					}

				}
			
				if($isPremium){
					$PremiumDomains[] = $Dom;
				}
				if($isLost){
					$LostDomains[] = $Dom;
				}
			}
		}
		
		return array('ND' => $PremiumDomains, 'LD' => $LostDomains);
		
	}
	
	function getMonthPublishers($idAccM, $MonthYear = false, $FLLimit = 20000){
		global $db;
		
		$LostPublishers = array();
		$NewPublishers = array();
		
		//$ArrayPubs = $this->getNewPublishers($idAccM, false, $MonthYear);
		//print_r($ArrayDomains);
		
		if($ArrayPubs != 0){
			
			foreach($ArrayPubs as $Pub){
				$isPremium = false;
				$isLost = false;
				$OneDay = false;
				
				$DatesArray = $this->getPublisherFormatLoadsPerDay($Pub, $MonthYear);
				
				foreach($DatesArray as $Date => $FL){
					
					if($FL >= $FLLimit){
						$isPremium = true;
						$OneDay = false;
						$isLost = false;
					}
					
					if($isPremium){
						if($FL < $FLLimit){
							if($OneDay){
								$isPremium = false;
								$isLost = true;
							}
							$OneDay = true;
						}
					}

				}
			
				if($isPremium){
					$NewPublishers[] = $Pub;
				}
				if($isLost){
					$LostPublishers[] = $Pub;
				}
			}
		}
		
		return array('NP' => $NewPublishers, 'LP' => $LostPublishers);
		
	}
	*/
	
	function getAccMDomains($idAccM, $MonthYear = false, $Premium = false){
		global $db;
		$SQLW = "";
		$CountD = 0;
		
		if($MonthYear !== false){
			if($Premium){
				if($MonthYear === false){
					$SQLW = " AND premium != '' ";	
				}else{
					$SQLW = " AND premium = '$MonthYear' AND premium_block = 0 ";	
				}
			}else{
				$SQLW = " AND regular = '$MonthYear' ";
			}
		}else{
			if($Premium){
				$SQLW = " AND premium != '0' AND premium_block = 0 ";
			}else{
				$SQLW = " AND regular != '0' ";
			}
		}
		
		$sql = "SELECT id FROM " . USERS . " WHERE AccM = '$idAccM'";
		$query = $db->query($sql);
		if($db->num_rows($query) > 0){
			//echo 'AB';
			$idstring = '';
			$or = '';
			while($Pub = $db->fetch_array($query)){
				$idPub = $Pub['id'];
				$idstring .= "$or idUser = '$idPub'";
				$or = " OR";
			}
		
		
			$sql = "SELECT COUNT(*) FROM " . SITES . " WHERE ($idstring) $SQLW";
			$CountD = $db->getOne($sql);
		}
		
		return $CountD;
	}
	
	function listAccMDomains($idAccM, $MonthYear = false, $Premium = false){
		global $db;
		$SQLW = "";
		$Doms = array();
		
		if($MonthYear !== false){
			if($Premium){
				if($MonthYear === false){
					$SQLW = " AND premium != '' ";	
				}else{
					$SQLW = " AND premium = '$MonthYear' AND premium_block = 0 ";	
				}
			}else{
				$SQLW = " AND regular = '$MonthYear' ";
			}
		}else{
			if($Premium){
				$SQLW = " AND premium != '0' AND premium_block = 0 ";
			}else{
				$SQLW = " AND regular != '0' ";
			}
		}
		
		$sql = "SELECT id FROM " . USERS . " WHERE AccM = '$idAccM'";
		$query = $db->query($sql);
		if($db->num_rows($query) > 0){
			//echo 'AB';
			$idstring = '';
			$or = '';
			while($Pub = $db->fetch_array($query)){
				$idPub = $Pub['id'];
				$idstring .= "$or idUser = '$idPub'";
				$or = " OR";
			}
		
		
			$sql = "SELECT * FROM " . SITES . " WHERE ($idstring) $SQLW ORDER BY id DESC";
			$query = $db->query($sql);
			if($db->num_rows($query) > 0){
				while($Dom = $db->fetch_array($query)){
					$sql = "SELECT user FROM " . USERS ." WHERE id = '" . $Dom['idUser'] . "' LIMIT 1";
					$Pub = $db->getOne($sql);
					$Dom['publishername'] = $Pub;
					$Doms[] = $Dom;
				}
			}
		}
		
		return $Doms;
	}
	
	function getAccMPublishers($idAccM, $MonthYear = false, $Premium = false){
		global $db;
		$SQLW = "";
		$CountD = 0;
		
		if($MonthYear !== false){
			if($Premium){
				$SQLW = " AND premium = '$MonthYear' ";
			}else{
				$SQLW = " AND regular = '$MonthYear' ";
			}
		}else{
			if($Premium){
				$SQLW = " AND premium != '0' ";
			}else{
				$SQLW = " AND regular != '0' ";
			}
		}
		
		$sql = "SELECT COUNT(*) FROM " . USERS . " WHERE AccM = '$idAccM' $SQLW";
		$CountD = $db->getOne($sql);
		
		
		return $CountD;
	}
	
	function listAccMPublishers($idAccM, $MonthYear = false, $Premium = false){
		global $db;
		$SQLW = "";
		$Pubs = array();
		
		if($MonthYear !== false){
			if($Premium){
				$SQLW = " AND premium = '$MonthYear' ";
			}else{
				$SQLW = " AND regular = '$MonthYear' ";
			}
		}else{
			if($Premium){
				$SQLW = " AND premium != '0' ";
			}else{
				$SQLW = " AND regular != '0' ";
			}
		}
		
		$sql = "SELECT * FROM " . USERS . " WHERE AccM = '$idAccM' $SQLW";
		$query = $db->query($sql);
		if($db->num_rows($query) > 0){
			while($Pub = $db->fetch_array($query)){
				$Pubs[] = $Pub;
			}
		}
		
		return $Pubs;
	}
	
	function getAccMLDomains($idAccM, $MonthYear = false){
		global $db;
		$SQLW = "";
		$CountD = 0;
		
		if($MonthYear !== false){
			$SQLW = " AND lost = '$MonthYear' ";
		}else{
			$SQLW = " AND lost != '0' ";
		}
		
		$sql = "SELECT id FROM " . USERS . " WHERE AccM = '$idAccM'";
		$query = $db->query($sql);
		if($db->num_rows($query) > 0){
			//echo 'AB';
			$idstring = '';
			$or = '';
			while($Pub = $db->fetch_array($query)){
				$idPub = $Pub['id'];
				$idstring .= "$or idUser = '$idPub'";
				$or = " OR";
			}
		
		
			$sql = "SELECT COUNT(*) FROM " . SITES . " WHERE ($idstring) $SQLW";
			$CountD = $db->getOne($sql);
		}
		
		return $CountD;
	}
	
	function getAccMLPublishers($idAccM, $MonthYear = false){
		global $db;
		$SQLW = "";
		$CountD = 0;
		
		if($MonthYear !== false){
			$SQLW = " AND lost = '$MonthYear' ";
		}else{
			$SQLW = " AND lost != '0' ";
		}
		
		$sql = "SELECT COUNT(*) FROM " . USERS . " WHERE AccM = '$idAccM' $SQLW";
		$CountD = $db->getOne($sql);
		
		return $CountD;
	}
	
	
	function getMonthRevenue($idAccM, $MonthYear = false){
		global $db;
		
		$Revenue = '0';
		$sql = "SELECT id FROM " . USERS . " WHERE AccM = '$idAccM'";
		$query = $db->query($sql);
		if($db->num_rows($query) > 0){
			$idstring = '';
			$or = '';
			while($Pub = $db->fetch_array($query)){
				$idPub = $Pub['id'];
				$idstring .= "$or idUser = '$idPub'";
				$or = " OR";
			}
			
			if($MonthYear){
				$FirstDay = $MonthYear . "-01";
				$LastDay = date("Y-m-t", strtotime($FirstDay));
			}else{
				$FirstDay = date("Y-m-") . '01';
				$LastDay = date("Y-m-t");
			}

			$sql = "SELECT SUM(Revenue) FROM " . STATS . " WHERE ($idstring) AND date BETWEEN '$FirstDay' AND '$LastDay'";
			$Revenue = $db->getOne($sql);
		}
		
		return $Revenue;
	}
	
	function getTopDomainsRevenue($idAccM, $MonthYear = false, $Top = 4){
		global $db;
		
		$ArraySites = array();
		$sql = "SELECT id FROM " . USERS . " WHERE AccM = '$idAccM'";
		$query = $db->query($sql);
		if($db->num_rows($query) > 0){
			$idstring = '';
			$or = '';
			while($Pub = $db->fetch_array($query)){
				$idPub = $Pub['id'];
				$idstring .= "$or idUser = '$idPub'";
				$or = " OR";
			}
			
			if($MonthYear){
				$FirstDay = $MonthYear . "-01";
				$LastDay = date("Y-m-t", strtotime($FirstDay));
			}else{
				$FirstDay = date("Y-m-") . '01';
				$LastDay = date("Y-m-t");
			}

			$sql = "SELECT SUM(Revenue) AS Revenue, idSite FROM " . STATS . " WHERE ($idstring) AND date BETWEEN '$FirstDay' AND '$LastDay' GROUP BY idSite ORDER BY Revenue DESC LIMIT $Top";
			$query = $db->query($sql);
			if($db->num_rows($query) > 0){
				while($Site = $db->fetch_array($query)){
					$ArraySites[$Site['idSite']] = $Site['Revenue'];
				}
			}
		}
		
		return $ArraySites;
	}
	
	
	function getDomainsRevenue($idSite, $MonthYear, $NotComplete = false){
		global $db;
		
		if($NotComplete){
			$FirstDay = $MonthYear . "-01";
			$LastDay = $MonthYear . date('-d');
		}else{
			$FirstDay = $MonthYear . "-01";
			$LastDay = date("Y-m-t", strtotime($FirstDay));
		}
		
		$sql = "SELECT SUM(Revenue) AS Revenue FROM " . STATS . " WHERE idSite = '$idSite' AND date BETWEEN '$FirstDay' AND '$LastDay'";
		return $db->getOne($sql);
	}
}	
	
?>