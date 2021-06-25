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
	
	$sql = "SELECT id, IP, idSite FROM " . PREBID_IMPRESION . " WHERE idUser = 0 AND CountryCode = '' LIMIT 200000";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Imp = $db->fetch_array($query)){
			$ipAddress = $Imp['IP'];
			$idImp = $Imp['id'];
			$idSite = $Imp['idSite'];
			
			if(!isset($idUsers[$idSite])){
				$sql = "SELECT idUser FROM " . SITES . " WHERE id = '$idSite' LIMIT 1";
				$idUser = $db->getOne($sql);
				$idUsers[$idSite] = $idUser;
			}else{
				$idUser = $idUsers[$idSite];
			}

			$Data = $reader->get($ipAddress);
			$Country = $Data['country']['iso_code'];
			$sql = "UPDATE " . PREBID_IMPRESION . " SET CountryCode = '$Country', idUser = '$idUser' WHERE id = '$idImp' LIMIT 1";
			//echo '<br/>';
			$db->query($sql);
		}
	}
	
	$sql = "SELECT " . PREBID_BIDS . ".id AS idBid, " . PREBID_BIDS . ".CPM, " . PREBID_BIDS . ".Currency, " . PREBID_IMPRESION . ".idUser, " . PREBID_IMPRESION . ".idSite, " . PREBID_IMPRESION . ".CountryCode, " . PREBID_IMPRESION . ".Date, " . PREBID_IMPRESION . ".Time, " . PREBID_IMPRESION . ".Mobile, " . PREBID_BIDS . ".Bidder
	FROM " . PREBID_BIDS . " 
	INNER JOIN " . PREBID_IMPRESION . " ON " . PREBID_BIDS . ".idImpesion = " . PREBID_IMPRESION . ".id AND " . PREBID_BIDS . ".Revised = 0
	WHERE " . PREBID_BIDS . ".Winner = 1  AND " . PREBID_IMPRESION . ".idUser != '' 
	"; //AND prebid_bids.Bidder = 'criteo' LIMIT 200000 
	//AND prebid_impresion.Date = '2019-03-12'
	
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Imp = $db->fetch_array($query)){
			
			$idBid = $Imp['idBid'];
			$Date = $Imp['Date'];
			$Hour = date('H', $Imp['Time']);
			$CC = $Imp['CountryCode'];
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
			
			
			
			$sql = "SELECT id FROM " . PREBID_REVENUE . " WHERE idUser = '$idUser' AND idSite = '$idSite' AND idTag = '$idTag' AND Date = '$Date' AND Mobile = '$Mobile' LIMIT 1";
			$idRev = $db->getOne($sql);
			if($idRev > 0){
				$sql = "UPDATE " . PREBID_REVENUE . " SET Amount = Amount + $CPM, Impressions = Impressions + 1 WHERE id = '$idRev' LIMIT 1";
			}else{
				$sql = "INSERT INTO " . PREBID_REVENUE . " (idUser, idSite, idTag, Mobile, Amount, Impressions, Date) VALUES ('$idUser', '$idSite', '$idTag', '$Mobile', '$CPM', 1, '$Date')";
			}
			$db->query($sql);
			
			
			$sql = "SELECT id FROM " . PREBID_REVENUE_HOURS . " WHERE idUser = '$idUser' AND idSite = '$idSite' AND idTag = '$idTag' AND Date = '$Date' AND Hour = '$Hour' AND Mobile = '$Mobile' LIMIT 1";
			$idRev = $db->getOne($sql);
			if($idRev > 0){
				$sql = "UPDATE " . PREBID_REVENUE_HOURS . " SET Amount = Amount + $CPM, Impressions = Impressions + 1 WHERE id = '$idRev' LIMIT 1";
			}else{
				$sql = "INSERT INTO " . PREBID_REVENUE_HOURS . " (idUser, idSite, idTag, Mobile, Amount, Impressions, Hour, Date) VALUES ('$idUser', '$idSite', '$idTag', '$Mobile', '$CPM', 1,  '$Hour', '$Date')";
			}
			$db->query($sql);
			
			
			$sql = "SELECT id FROM " . PREBID_REVENUE_COUNTRY . " WHERE idUser = '$idUser' AND idSite = '$idSite' AND idTag = '$idTag' AND Date = '$Date' AND CountryCode = '$CC' AND Mobile = '$Mobile' LIMIT 1";
			$idRev = $db->getOne($sql);
			if($idRev > 0){
				$sql = "UPDATE " . PREBID_REVENUE_COUNTRY . " SET Amount = Amount + $CPM, Impressions = Impressions + 1 WHERE id = '$idRev' LIMIT 1";
			}else{
				$sql = "INSERT INTO " . PREBID_REVENUE_COUNTRY . " (idUser, idSite, idTag, Mobile, CountryCode, Amount, Impressions, Date) VALUES ('$idUser', '$idSite', '$idTag', '$Mobile', '$CC', '$CPM', 1, '$Date')";
			}
			$db->query($sql);
			
			
			$sql = "UPDATE " . PREBID_BIDS . " SET Revised = 1 WHERE id = '$idBid' LIMIT 1";
			$db->query($sql);
			
			
		}
	}
	
	//exit(0);
	
	$sql = "SELECT id, idUser, idSite, CountryCode, Mobile, Date, Time FROM " . PREBID_IMPRESION . " WHERE Revised = 0 ORDER BY id DESC LIMIT 200000";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Imp = $db->fetch_array($query)){
			
			
			$idImp = $Imp['id'];
			$Date = $Imp['Date'];
			$Hour = date('H', $Imp['Time']);
			$CC = $Imp['CountryCode'];
			$idSite = $Imp['idSite'];
			$idUser = $Imp['idUser'];
			$idTag = 0; //$idTag = intval($Imp['idTag']); 
			$Mobile = intval($Imp['Mobile']);
			
			$sql = "SELECT id FROM " . PREBID_FL . " WHERE idUser = '$idUser' AND idSite = '$idSite' AND idTag = '$idTag' AND Date = '$Date' AND Mobile = '$Mobile' LIMIT 1";
			$idFl = $db->getOne($sql);
			if($idFl > 0){
				$sql = "UPDATE " . PREBID_FL . " SET formatLoads = formatLoads + 1 WHERE id = '$idFl' LIMIT 1";
			}else{
				$sql = "INSERT INTO " . PREBID_FL . " (idUser, idSite, idTag, Mobile, formatLoads, Date) VALUES ('$idUser', '$idSite', '$idTag', '$Mobile', 1, '$Date')";
			}
			$db->query($sql);
			
			
			$sql = "SELECT id FROM " . PREBID_FL_HOURS . " WHERE idUser = '$idUser' AND idSite = '$idSite' AND idTag = '$idTag' AND Date = '$Date' AND Mobile = '$Mobile' AND Hour = '$Hour' LIMIT 1";
			$idFl = $db->getOne($sql);
			if($idFl > 0){
				$sql = "UPDATE " . PREBID_FL_HOURS . " SET formatLoads = formatLoads + 1 WHERE id = '$idFl' LIMIT 1";
			}else{
				$sql = "INSERT INTO " . PREBID_FL_HOURS . " (idUser, idSite, idTag, Mobile, formatLoads, Hour, Date) VALUES ('$idUser', '$idSite', '$idTag', '$Mobile', 1, '$Hour', '$Date')";
			}
			$db->query($sql);
			
			
			$sql = "SELECT id FROM " . PREBID_FL_COUNTRY . " WHERE idUser = '$idUser' AND idSite = '$idSite' AND idTag = '$idTag' AND Date = '$Date' AND Mobile = '$Mobile' AND CountryCode = '$CC' LIMIT 1";
			$idFl = $db->getOne($sql);
			if($idFl > 0){
				$sql = "UPDATE " . PREBID_FL_COUNTRY . " SET formatLoads = formatLoads + 1 WHERE id = '$idFl' LIMIT 1";
			}else{
				$sql = "INSERT INTO " . PREBID_FL_COUNTRY . " (idUser, idSite, idTag, Mobile, formatLoads, CountryCode, Date) VALUES ('$idUser', '$idSite', '$idTag', '$Mobile', 1, '$CC', '$Date')";
			}
			$db->query($sql);
			
			$sql = "UPDATE " . PREBID_IMPRESION . " SET Revised = 1 WHERE id = '$idImp' LIMIT 1";
			$db->query($sql);
			
		}
	}
	
	
	echo 'OK';
	
	exit(0);
?><html>
    <head>
        <title>Cron OK</title>
        <meta http-equiv="refresh" content="5" />
    </head>
    <body>

    </body>
</html>