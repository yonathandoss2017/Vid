<?php
	
function getCountrySpotXID($CountryName, $Token){
	
	$curl2 = curl_init();
	curl_setopt_array($curl2, array(
	  CURLOPT_URL => 'https://publisher-api.spotxchange.com/1.1/geography/country?$search=' . urlencode($CountryName),
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_VERBOSE => false,
	  CURLOPT_HTTPHEADER => array(
		"Content-Type: application/json",
	    "Authorization: Bearer $Token"
	  ),
	));
	
	$Json = curl_exec($curl2);
	curl_close($curl2);
	
	//echo $Json;
				
	$CountriesData = json_decode($Json);
	
	foreach($CountriesData->value as $Country){
		if($Country->level == 'Country'){
			return $Country->id;
		}
	}
	return false;
}

function getSpotXToken(){
	$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://publisher-api.spotxchange.com/1.1/token",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS => "client_id=API-cHVibGlzaGVyLzIxODQ0Mw%3D%3D&client_secret=ed6b589134ac4d499e1cc969d6aa193c6d34edab&grant_type=refresh_token&refresh_token=251b3eb9916786f8220f35aa8cfe2572def5391e",
	  CURLOPT_HTTPHEADER => array(
	    "Content-Type: application/x-www-form-urlencoded"
	  ),
	));
	
	$Json = curl_exec($curl);
	
	curl_close($curl);
	
	$Decoded = json_decode($Json);
	$Token = $Decoded->value->data->access_token;
	return $Token;
}
	
?>