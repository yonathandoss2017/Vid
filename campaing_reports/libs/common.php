<?php
function setCacheCampaings(){
	global $mem_var, $db;
	
	$WhiteList = array();
		
	$sql = "SELECT * FROM interactivecampaings";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($W = $db->fetch_array($query)){
			$WhiteList[$W['idCampaing']][] = $W['idSite'];
		}
	}
	
	$mem_var->set('WhiteList', $WhiteList, 21600);
	
	return $WhiteList;
}	
	