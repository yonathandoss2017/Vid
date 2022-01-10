<?php
	$originalSrting = 'ad_type=Video&adomain=vidoomy.com&c=ES&category=IAB1&crid=16_3586644756&deal=&domain=vidoomy.com&dsp=TestCommissions&dsp_ssp=TestCommissions&dt=2&gdpr=0&gdprcs=0&gmoney=10&money=9&os=Unknown&p=123&p_id=123&s=vidoomy.com&seat=1&size=400%2A225&sspid=62138&sync=0&zid=15031';
	
	$base64string = base64_encode(gzdeflate($originalSrting));
	
	echo $base64string;
	
	echo "\n\n";
	
	echo gzinflate(base64_decode($base64string));
	
	echo "\n\n";