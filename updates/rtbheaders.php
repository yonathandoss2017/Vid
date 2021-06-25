<?php
	$url = "https://rtb.vidoomy.com/?id=2577739972002413288&ad_type=0&mimes[]=video/mp4&minduration=1&maxduration=600&pos=1&protocols[]=1&w=400&h=225&skip=1&ip=139.47.103.143&ua=Mozilla%2F5.0+%28Macintosh%3B+Intel+Mac+OS+X+10_15_2%29+AppleWebKit%2F537.36+%28KHTML%2C+like+Gecko%29+Chrome%2F81.0.4044.138+Safari%2F537.36&language=&devicetype=2&country=ES&publisher_id=99&site_domain=vidoomy.com&site_page=88&coppa=&gdpr=&us_privacy=";
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_VERBOSE, 0);
	curl_setopt($ch, CURLOPT_HEADER, true);
	
	curl_setopt($ch, CURLOPT_URL, $url);
	
	echo  $response = curl_exec($ch);
	//exit();
	// Then, after your curl_exec call:
	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	$header = substr($response, 0, $header_size);
	$body = substr($response, $header_size);
	
	