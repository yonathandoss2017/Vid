<?php
$username = 'lum-customer-vidoomy-zone-zone1';
$password = 'pko5mtrvy6xn';
$port = 22225;
$session = mt_rand();
$super_proxy = 'zproxy.lum-superproxy.io';
$curl = curl_init('http://lumtest.com/myip.json');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_PROXY, "http://$super_proxy:$port");
curl_setopt($curl, CURLOPT_PROXYUSERPWD, "$username-session-$session:$password");
$result = curl_exec($curl);
curl_close($curl);
if ($result)
    echo $result;