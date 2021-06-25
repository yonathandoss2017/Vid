<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$dbuser2 = "root";
	$dbpass2 = "Jz8eDbamcNx3TskWzrjzH7g";
	$dbhost2 = "vidoomy-production.cpijmqdfbof9.eu-west-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	$db3 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
function slugify($text)
{
  // replace non letter or digits by -
  $text = preg_replace('~[^\pL\d]+~u', '-', $text);

  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);

  // trim
  $text = trim($text, '-');

  // remove duplicate -
  $text = preg_replace('~-+~', '-', $text);

  // lowercase
  $text = strtolower($text);

  if (empty($text)) {
    return 'n-a';
  }

  return $text;
}	

	$sql = "SELECT * FROM vidoomy.publisher WHERE finance_account_id IS NULL;";
	$query = $db2->query($sql);
	if($db2->num_rows($query) > 0){
		while($P = $db2->fetch_array($query)){
			$idU = $P['user_id'];
			$idP = $P['id'];
			
			$sql = "SELECT * FROM user WHERE id = $idU LIMIT 1";		
			$query2 = $db3->query($sql);
			$U = $db3->fetch_array($query2);
			
			$Name = $U['name'];
			$Last = $U['last_name'];
			
			$Slug = slugify($Name . ' ' . $Last);
			$sql = "INSERT INTO `vidoomy`.`finance_account` (`country_id`, `bank_country_id`, `currency_id`, `created_by_id`, `name`, `status`, `fiscal_status`, `vat`, `irpf`, `is_exceptional`, `comments`, `payment_type`, `amount`, `created_at`, `paypal_account`, `slug`, `pending_amount`) VALUES ('999', '999', '1', '1', '$Name $Last', '3', '0', '0', '0', '0', 'Alta Automática', '2', '100', 'TIMESTAMP', '-', '$Slug', '0');";
			
			echo $sql . "\n";
			
			
			$db3->query($sql);
			$FinancialId = mysqli_insert_id($db3->link);
			
			
			$sql = "UPDATE publisher SET finance_account_id = $FinancialId WHERE id = $idP LIMIT 1";
			$db3->query($sql);
			
			echo $sql . "\n";
			
			//exit(0);
		}
	}