<?php
	
	$Js = '{"id":"3554b.131fc.e5d6","name":"Campaign_Test_Start_Time","paused":false,"status":"Pending","honor_channel_price_floor":true,"price_floor_currency":"USD","disable_ad_blocking":true,"automated_guaranteed":false,"ad_review_enabled":false,"deal_originator":218443,"priority":1,"reporting_timezone":"+0000","start_datetime":{"date":"2020-09-22","time":"11:40 AM","timezone":"+0000"},"end_datetime":null,"frequency_capping":{"enabled":false},"share_of_voice":{"enabled":false},"pacing":{"enabled":false},"fixed_cpm":"4.23","dsp_partner_id":7025,"fixed_cpm_type":"Fixed","source":"Programmatic Direct","creatives":[{"name":"Campaign Creative","id":386844,"weight":100,"url":null,"url_type":"OpenRTB 2.2","request_type":"Server-Side","supports_ad_id":true,"third_party_beacons":[]}],"targeting_options":[],"third_party_beacons":[],"dayparting":null,"preferred_slot":null}';
	
	print_r(json_decode($Js));
	
?>