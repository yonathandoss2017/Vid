<?php
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/constantes.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	require('/var/www/html/login/common.lib.php');
	
	require('/var/www/html/ads/MaxMind-DB-Reader-php-master/autoload.php');
	require_once '/var/www/html/ads/MaxMind-DB-Reader-php-master/src/MaxMind/Db/Reader.php';
	require_once '/var/www/html/ads/MaxMind-DB-Reader-php-master/src/MaxMind/Db/Reader/Decoder.php';
	require_once '/var/www/html/ads/MaxMind-DB-Reader-php-master/src/MaxMind/Db/Reader/InvalidDatabaseException.php';
	require_once '/var/www/html/ads/MaxMind-DB-Reader-php-master/src/MaxMind/Db/Reader/Metadata.php';
	use MaxMind\Db\Reader;
	
	$databaseFile = '/var/www/html/ads/MaxMind/GeoIP2-Country.mmdb';
	$reader = new Reader($databaseFile);
	$idUsers = array();
	
	
	echo $sql = "SELECT " . PREBID_BIDS . ".id AS idBid, " . PREBID_BIDS . ".CPM, " . PREBID_BIDS . ".Currency, " . PREBID_IMPRESION . ".idUser, " . PREBID_IMPRESION . ".idSite, " . PREBID_BIDS . ".Bidder, " . PREBID_IMPRESION . ".Date, " . PREBID_IMPRESION . ".Time, " . PREBID_IMPRESION . ".Mobile  
	FROM " . PREBID_BIDS . " 
	INNER JOIN " . PREBID_IMPRESION . " ON " . PREBID_BIDS . ".idImpesion = " . PREBID_IMPRESION . ".id
	WHERE " . PREBID_BIDS . ".Winner = 1  AND " . PREBID_IMPRESION . ".idUser != '' AND " . PREBID_IMPRESION . ".Date = '2019-03-12'";
	 //AND prebid_bids.Bidder = 'criteo' AND prebid_impresion.Date = '2019-03-08'
	 //AND " . PREBID_BIDS . ".Revised = 0
	
	exit(0);
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Imp = $db->fetch_array($query)){
			
			$idBid = $Imp['idBid'];
			$Date = $Imp['Date'];
			$Hour = date('H', $Imp['Time']);
			$idSite = $Imp['idSite'];
			$idUser = $Imp['idUser'];
			$idTag = intval($Imp['idTag']);
			$Mobile = intval($Imp['Mobile']);
			if($Imp['Currency'] == 'EUR'){
				$CPM = $CPM * 1.15;
			}
			$CPM = $Imp['CPM'] / 1000;
			
			$BidderId = 0;
			$BidderName = $Imp['Bidder'];
			if($BidderName == 'appnexus'){
				$BidderId = 1;
			}elseif($BidderName == 'criteo'){
				$BidderId = 2;
			}elseif($BidderName == 'aol'){
				$BidderId = 3;
			}elseif($BidderName == 'pulsepoint'){
				$BidderId = 4;
			}elseif($BidderName == 'smartserver'){
				$BidderId = 5;
			}elseif($BidderName == 'pubmatic'){
				$BidderId = 6;
			}
			
			
			$sql = "SELECT id FROM " . PREBID_REVENUE_BIDDER . " WHERE idUser = '$idUser' AND idSite = '$idSite' AND idTag = '$idTag' AND Date = '$Date' AND Mobile = '$Mobile' AND Bidder = '$BidderId' LIMIT 1";
			$idRev = $db->getOne($sql);
			if($idRev > 0){
				$sql = "UPDATE " . PREBID_REVENUE_BIDDER . " SET Amount = Amount + $CPM, Impressions = Impressions + 1 WHERE id = '$idRev' LIMIT 1";
			}else{
				$sql = "INSERT INTO " . PREBID_REVENUE_BIDDER . " (idUser, idSite, idTag, Mobile, Bidder, Amount, Impressions, Date) VALUES ('$idUser', '$idSite', '$idTag', '$Mobile', '$BidderId', '$CPM', 1, '$Date')";
			}
			$db->query($sql);

		}
	}
	
	echo 'OK';
	
	exit(0);
?>