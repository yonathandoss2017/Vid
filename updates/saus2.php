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

	$sql = "SELECT 
	u.id, 
	u.user, 
	u.ef, 
	u.nick, 
	u.email, 
	CONCAT(u.name,' ',u.lastname) AS nombre, 
	u.phone, 
	u.movil, 
	u.sykpe, 
	u.nifcif, 
	u.company,
	c.country_name,
	u.province,
	u.city,
	u.cp,
	u.address,
	u.currency,
	u.paymenttype,
	u.account,
	u.bankname,
	u.bankaddress,
	u.bankcountry,
	u.bankcurrency,
	u.iban,
	u.swift,
	u.netterms,
	u.amount,
	u.date
	
	FROM `users` u
	INNER JOIN countries c ON c.id = u.country 



	
	WHERE  AccM != 15 AND AccM != 9999 AND deleted = 0"; //`date` BETWEEN '2019-11-01' AND '2019-11-30'AND
	
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		echo "<table><tr>
			<td>Usuario</td><td>Nick</td><td>Nombre</td><td>Email</td><td>Empresa</td><td>Pais</td><td>Provincia</td><td>Ciudad</td><td>Direccion</td><td>Codigo Postal</td><td>Estado Fiscal</td><td>NIF/CIF</td><td>Tipo de pago</td><td>Moneda</td><td>Cuenta</td><td>Dirección del Banco</td><td>NetTerms</td><td>Importe mínimo</td><td>Nombre del Banco</td><td>País del banco</td><td>IBAN</td><td>SWIFT</td><td>Día de Registro</td><td>Día generó ganancias</td></tr>";
		while($User = $db->fetch_array($query)){
			$idUser = $User['id'];
			
			$sql = "SELECT COUNT(*) FROM stats WHERE Date < '2019-10-01' AND Coste > 0 AND idUser = '$idUser' ";
			if($db->getOne($sql) == 0 && $idUser != 3583){
				$sql = "SELECT COUNT(*) FROM stats WHERE Date BETWEEN '2019-10-01' AND '2019-11-30' AND Coste > 0 AND idUser = '$idUser' ";
				if($db->getOne($sql) > 0){
				
					$sql = "SELECT Date FROM stats WHERE Date BETWEEN '2019-10-01' AND '2019-11-30' AND Coste > 0 AND idUser = '$idUser' ORDER BY Date ASC LIMIT 1";
					$DateStated = $db->getOne($sql);
					$ArD = explode('-',$DateStated);
					$DateStated = $ArD[2] . '/' . $ArD[1] . '/' . $ArD[0];
					
					$ArD = explode('-',$User['date']);
					$DateReg = $ArD[2] . '/' . $ArD[1] . '/' . $ArD[0];
					
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
					if($User['currency'] == 1){
						$CU = "USD";
					}else{
						$CU = "EUR";
					}
					$idBC = $User['bankcountry'];
		
					$sql = "SELECT country_name FROM countries WHERE id = '$idBC' LIMIT 1";
					$BankkCountry = $db->getOne($sql);
					
					echo "<tr><td>";		
					echo $User['user'];
					echo "</td><td>";		
					echo $User['nick'];
					echo "</td><td>";
					echo $User['nombre'];
					echo "</td><td>";
					echo $User['email'];
					echo "</td><td>";
					echo $User['company'];
					echo "</td><td>";
					echo $User['country_name'];
					echo "</td><td>";
					echo $User['province'];
					echo "</td><td>";
					echo $User['city'];
					echo "</td><td>";
					echo $User['address'];
					echo "</td><td>";
					echo $User['cp'];
					echo "</td><td>";
					echo $EF;
					echo "</td><td>";
					echo $User['nifcif'];
					echo "</td><td>";
					echo $PT;
					echo "</td><td>";
					echo $CU;
					echo "</td><td>";
					echo $User['account'];
					echo "</td><td>";
					echo $User['bankaddress'];
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
					echo $User['swift'];
					echo "</td><td>";
					echo $DateReg;
					echo "</td><td>";
					echo $DateStated;
					echo "</td></tr>";
				}
			}
		}
	}

	
?></table>