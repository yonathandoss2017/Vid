<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 1);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/common.lib.php');
	require('/var/www/html/login/admin/lkqdimport/common.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
function generateRandomString($length = 10) {
    $characters = 'abcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}	
	
	$sql = "SELECT * FROM users2 ORDER BY id ASC";
	
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($User = $db->fetch_array($query)){
			$idU = $User['id'];
			$newuser = generateRandomString(rand(3,6));
			$newemail = $newuser . '@email.com';
			$newname = ucfirst(generateRandomString(rand(4,8)));
			$newlast = ucfirst(generateRandomString(rand(2,5)));
			$newcompany = ucfirst(generateRandomString(rand(8,12)));
			$newphone = '692' . rand(100,999) . rand(100,999);
			$newskyop = generateRandomString(rand(5,16));
			if(rand(1,9) == 2){
				$newnick = generateRandomString(rand(3,6));
			}else{
				$newnick = '';
			}
			$newpass = md5(time());
			$newnif = rand(100,999) . rand(100,999) . rand(100,999);
			$newcp = rand(10000,20000);
			$sql = "UPDATE users2 SET user = '$newuser', nick = '$newnick', password = '$newpass', email = '$newemail', name = '$newname', lastname = '$newlast', company = '$newcompany', phone = '$newphone', movil = '$newphone', sykpe = '$newskyop', nifcif = '$newnif', province = 'NA', city = 'NA', cp = '$newcp', address = 'Address', account = 'Pending' WHERE id = '$idU' LIMIT 1";
			$db->query($sql);
			echo $sql;
			//exit(0);
		}
	}