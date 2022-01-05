<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);

	$Start = 601;
	$Cnt = 201;

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
	
	for($F = 1; $F <= $Cnt; $F++){
		
		if($Start > 99){
			$Len = 5;
		}else{
			$Len = 6;
		}
		
		$Code = 'GO' . $Start . strtoupper(generateRandomString($Len));
		
		//$sql = "INSERT INTO `dev_test_front` (`id`, `Name`, `Last`, `Code`, `Start`, `Submit1`, `Submit2`, `Submit3`, `Submit4`, `Submit5`, `Finish`, `Activity`) VALUES (NULL, 'FRONT$Start', 'F$Start', '$Code', '0', '0', '0', '0', '0', '0', '0', '');";
		//$sql = "INSERT INTO `dev_test_front_new` (`id`, `Name`, `Last`, `Code`, `Start`, `Submit1`, `Submit2`, `Submit3`, `Submit4`, `Submit5`, `Finish`, `Activity`) VALUES (NULL, 'FULL$Start', 'F$Start', '$Code', '0', '0', '0', '0', '0', '0', '0', '');";
		$sql = "INSERT INTO `dev_test_go` (`id`, `Name`, `Last`, `Code`, `Start`, `Submit1`, `Submit2`, `Submit3`, `Submit4`, `Submit5`, `Finish`, `Activity`) VALUES (NULL, 'GOLANG$Start', 'G$Start', '$Code', '0', '0', '0', '0', '0', '0', '0', '');";
		$db->query($sql);
		
		echo "$Start: $Code \n";
		
		$Start++;
	}
	
	/*
	$handle = fopen("test_codes_vidoomy_front_2.txt", "r");
	if ($handle) {
	    while (($line = fgets($handle)) !== false) {
		    $Ar = explode(':',$line);
		    $Code = trim($Ar[1]);
		    
		    $sql = "INSERT INTO `dev_test_front` (`id`, `Name`, `Last`, `Code`, `Start`, `Submit1`, `Submit2`, `Submit3`, `Submit4`, `Submit5`, `Finish`, `Activity`) VALUES (NULL, 'LATAM$Start', 'L$Start', '$Code', '0', '0', '0', '0', '0', '0', '0', '');";
		$db->query($sql);
		    echo $Code . "\n";
		    
		    $Start++;
		}
	}
	*/