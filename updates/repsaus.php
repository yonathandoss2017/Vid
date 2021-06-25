<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 1);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/common.lib.php');
	require '/var/www/html/site/include/PHPMailer/PHPMailerAutoload.php';
	require('/var/www/html/login/admin/lkqdimport/common.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	mysqli_set_charset($db->link,'utf8');
	
	$sql = "SELECT users.*, countries.country_name FROM users INNER JOIN countries ON countries.id = users.country
	WHERE
		users.deleted = 0 AND users.AccM != 15 AND users.AccM != 9999
	ORDER BY users.id ASC";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		echo "<table><tr>
			<td>Usuario</td><td>Nick</td><td>Estado Fiscal</td><td>Email</td><td>Empresa</td><td>Pais</td><td>Provincia</td><td>Direccion</td><td>NIF/CIF</td><td>Ciudad</td><td>Codigo Postal</td><td>Tipo de pago</td><td>Cuenta</td><td>Dirección del Banco</td><td>SWIFT</td><td>NetTerms</td><td>Importe mínimo</td><td>Nombre del Banco</td><td>País del banco</td><td>IBAN</td><td>Generó ingresos último año</td></tr>";
		while($User = $db->fetch_array($query)){
			$idUser = $User['id'];
			$sql = "SELECT COUNT(*) FROM stats WHERE idUser = '$idUser' AND stats.Revenue > 0 AND stats.Date >= '2019-01-01'";
			if($db->getOne($sql) > 0){
				$Gen = 'Si';
			}else{
				$Gen = 'No';
			}
			if($User['ef'] == 1){
				$EF = "Empresa";
			}else{
				$EF = "Autonomo";
			}
			if($User['paymenttype'] == 1){
				$PT = "Paypal";
			}else{
				$PT = "Transferencia";
			}
			$idBC = $User['bankcountry'];

			$sql = "SELECT country_name FROM countries WHERE id = '$idBC' LIMIT 1";
			$BankkCountry = $db->getOne($sql);
			
			echo "<tr><td>";		
			echo $User['user'];
			echo "</td><td>";		
			echo $User['nick'];
			echo "</td><td>";		
			echo $EF;
			echo "</td><td>";
			echo $User['email'];
			echo "</td><td>";
			echo $User['company'];
			echo "</td><td>";
			echo $User['country_name'];
			echo "</td><td>";
			echo $User['province'];
			echo "</td><td>";
			echo $User['address'];
			echo "</td><td>";
			echo $User['nifcif'];
			echo "</td><td>";
			echo $User['city'];
			echo "</td><td>";
			echo $User['cp'];
			echo "</td><td>";
			echo $PT;
			echo "</td><td>";
			echo $User['account'];
			echo "</td><td>";
			echo $User['bankaddress'];
			echo "</td><td>";
			echo $User['swift'];
			echo "</td><td>";
			echo $User['netterms'];
			echo "</td><td>";
			echo $User['amount'];
			echo "</td><td>";
			echo $User['bankname'];
			echo "</td><td>";
			echo $BankkCountry;
			echo "</td><td>";
			echo $User['iban'];
			echo "</td><td>";
			echo $Gen;
			echo "</td></tr>";
		}
	}

	
?></table>