<?php
	session_start();
	define('CONST',1);
	if(isset($_POST['user'])){
		if($_POST['user']=="admin" && $_POST['pass']=="Vidoomy%1"){
			$_SESSION['Admin'] = 1;
		}
	}
	if($_SESSION['Admin']!=1){
		header('Location: login.php');
		exit(0);
	}
	require('../config.php');
	require('../db.php');
	require('../constantes.php');
	require('../common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);

	if($_GET['del'] > 0){
		$idDel = intval($_GET['del']);
		$sql = "DELETE FROM " . USERS . " WHERE id = '$idDel' LIMIT 1";
		$db->query($sql);
		header('Location: index.php');
		exit(0);
	}
	
	if(!empty($_POST['agrega'])){
		if(!empty($_POST['user'])){
			if(!empty($_POST['password'])){
				if($_POST['password'] == $_POST['password2']){
					
					$user = my_clean($_POST['user']);
					$password = md5($_POST['password']);
					$email = my_clean($_POST['email']);
					$name = my_clean($_POST['name']);
					$lastname = my_clean($_POST['lastname']);
					$phone = my_clean($_POST['phone']);
					$movil = my_clean($_POST['movil']);
					$sykpe = my_clean($_POST['sykpe']);
					$ef = intval($_POST['ef']);
					$nifcif = my_clean($_POST['nifcif']);
					$company = my_clean($_POST['company']);
					$country = my_clean($_POST['country']);
					$province = my_clean($_POST['province']);
					$city = my_clean($_POST['city']);
					$cp = my_clean($_POST['cp']);
					$address = my_clean($_POST['address']);
					$currency = my_clean($_POST['currency']);
					$idlkqd = my_clean($_POST['idlkqd']);
					$ssid = my_clean($_POST['ssid']);
					$time = time();
					$date = date('Y-m-d');
					
					$sql = "INSERT INTO " . USERS . " (user, password, email, name, lastname, phone, movil, sykpe, ef, nifcif, company, country, province, city, cp, address, currency, LKQD_id, SS_id, lastlogin, showi, time, date) 
						VALUES ('$user', '$password','$email', '$name', '$lastname', '$phone', '$movil', '$sykpe', '$ef', '$nifcif', '$company', '$country', '$province', '$city', '$cp', '$address', '$currency', '$idlkqd', '$ssid', '0', '0', '$time', '$date')";
					$db->query($sql);
					$exito = 1;

				}else{$Error = "La confirmación de la contraseña debe coincidir.";}
			}else{$Error = "Debe completar la Constraseña.";}
		}else{$Error = "Debe completar el Usuario.";}
	}
	if(!empty($_POST['guarda'])){
		if(!empty($_POST['user'])){
			$passu = '';
			$sigue = true;
			if(!empty($_POST['password'])){
				if($_POST['password'] == $_POST['password2']){
					$newpassword = md5($_POST['password']);
					$passu = ", password = '$newpassword'";
				}else{
					$Error = "La confirmación de la contraseña debe coincidir.";
					$sigue = false;
				}
			}
					
			$user = my_clean($_POST['user']);
			$email = my_clean($_POST['email']);
			$name = my_clean($_POST['name']);
			$lastname = my_clean($_POST['lastname']);
			$phone = my_clean($_POST['phone']);
			$movil = my_clean($_POST['movil']);
			$sykpe = my_clean($_POST['sykpe']);
			$ef = intval($_POST['ef']);
			$nifcif = my_clean($_POST['nifcif']);
			$company = my_clean($_POST['company']);
			$country = my_clean($_POST['country']);
			$province = my_clean($_POST['province']);
			$city = my_clean($_POST['city']);
			$cp = my_clean($_POST['cp']);
			$address = my_clean($_POST['address']);
			$idlkqd = my_clean($_POST['idlkqd']);
			$ssid = my_clean($_POST['ssid']);
			$time = time();
			$date = date('Y-m-d');
					
			$sql = "UPDATE " . USERS . " SET user = '$user'$passu , email = '$email', name = '$name', lastname = '$lastname', phone = '$phone', movil = '$movil', sykpe = '$sykpe', ef = '$ef', nifcif = '$nifcif', company = '$company', country = '$country', province = '$province', city = '$city', cp = '$cp', address = '$address', LKQD_id = '$idlkqd', SS_id = '$ssid' WHERE id = '" . intval($_GET['edit']) . "' LIMIT 1";
			$db->query($sql);
			$exito = 1;
		}else{$Error = "Debe completar el Usuario.";}
	}
	if(!empty($_POST['borrar'])){
		print_r($_POST);
	}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Vidoomy - Admin Area</title>
<link rel="stylesheet" type="text/css" href="css/theme.css" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
<script>
   var StyleFile = "theme" + document.cookie.charAt(6) + ".css";
   document.writeln('<link rel="stylesheet" type="text/css" href="css/' + StyleFile + '">');
</script>
<script type="text/javascript">
nn = 1;
function agregaImagen(){
	nn++;
	document.getElementById('imagen' + nn).style.display = 'block';
	if(nn>=10){
		document.getElementById('agregaimagen').style.display = 'none';
	}
}
function jqCheckAll( id, name, flag ){
   if (flag == false){
      $("form#" + id + " INPUT[@name=" + name + "][type='checkbox']").attr('checked', false);
   }else{
	  $("form#" + id + " INPUT[@name=" + name + "][type='checkbox']").attr('checked', true);
   }
}
</script>
<!--[if IE]>
<link rel="stylesheet" type="text/css" href="css/ie-sucks.css" />
<![endif]-->
</head>

<body>
	<div id="container">
    	<div id="header">
        	<h2>Vidoomy - Admin Area</h2>
    <div id="topmenu">
            	<ul>
                	<li class="current"><a href="index.php">Usuarios</a></li>
                    <!--<li><a href="config.php">Configuración General</a></li>-->
              </ul>
          </div>
      </div>
      </div>
        <div id="wrapper">
            <div id="content">
				<?php
					if(empty($_GET['edit'])){
				?>
				<div id="box">
					<h3>Usuarios</h3>
					<div></div>

					<form action="" method="post" name="lista" id="lista">
                	<table width="100%">
						<thead>
							<tr>
                            	<th><input type="checkbox" onchange="jqCheckAll('lista', 'borra', this.checked);"></th>
								<th width="40px"><a href="#">ID<img src="img/icons/arrow_down_mini.gif" width="16" height="16" align="absmiddle" /></a></th>
								<th><a href="#">Usuario</a></th>
								<th width="90px"><a href="#">Agregado</a></th>
								<th width="90px"><a href="#">Último Ingreso</a></th>
                                <th width="80px"><a href="#">Acciones</a></th>
                            </tr>
						</thead>
						<tbody><?php
							$sql = "SELECT * FROM " . USERS . " ORDER BY id DESC";
							$query = $db->query($sql);
							if($db->num_rows($query) > 0){
								while($User = $db->fetch_array($query)){
									$aR = explode('-',$User['date']);
									$Fecha = $aR[2] . '/' . $aR[1] . '/' . $aR[0];
									?><tr>
										<td class="a-center"><input type="checkbox" value="<?php echo $User['id']; ?>" name="borra[]" /></td>
										<td class="a-center"><?php echo $User['id']; ?></td>
										<td><?php echo $User['user']; ?></td>
										<td class="a-center"><?php echo $Fecha; ?></td>
										<td class="a-center"><?php if($User['lastlogin'] > 0 ) { echo date('d/m/Y',$User['lastlogin']); } else { echo 'NA'; } ?></td>
										<td class="a-center">
											<a href="tags.php?edit=<?php echo $User['id']; ?>" title="Editar Tags"><img src="img/icons/report_link.png" alt="Ver/Editar" width="16" height="16" /></a>
											<a href="index.php?edit=<?php echo $User['id']; ?>" title="Ver/Editar"><img src="img/icons/page_white_edit.png" alt="Ver/Editar" width="16" height="16" /></a>
											<a onclick="return confirm('¿Esta seguro de que desea ELIMINAR este usuario?')" href="index.php?del=<?php echo $User['id']; ?>" title="Eliminar"><img src="img/icons/cancel.png" alt="Eliminiar" width="16" height="16" /></a>
										</td>
									</tr><?php
								}
							}
							?>
						</tbody>
					</table>
					</form>
                    <?php /*<div id="pager">
                    	Page <a href="#"><img src="img/icons/arrow_left.gif" width="16" height="16" /></a> 
                    	<input size="1" value="1" type="text" name="page" id="page" /> 
                    	<a href="#"><img src="img/icons/arrow_right.gif" width="16" height="16" /></a>of 42
                    pages | Total <strong>420</strong> records found
                    </div>*/ ?>
                </div>
                <br />
                <div id="box">
                	<h3 id="adduser">Agregar Usuario</h3>
					<?php if(!empty($Error)){?><div style="text-align:center; margin:auto; color:red; padding-top:5px;"><?php echo $Error; ?></div><?php } ?>
                    <form id="form" action="index.php" method="post" enctype="multipart/form-data">
                      <fieldset id="personal">
                        <legend>Información del Usuario</legend>
                        <label for="user">Usuario: </label>
                        <input name="user" id="user" type="text" tabindex="2" />
                        <br />
                        <label for="email">Email: </label>
                        <input name="email" id="email" type="text" tabindex="2" />
                        <br /> 
                        <label for="password">Contraseña: </label>
                        <input name="password" id="password" type="password" tabindex="2" />
                        <br /> 
                        <label for="password2">Confirmar Contraseña: </label>
                        <input name="password2" id="password2" type="password" tabindex="2" />
                        <br /> 
						<label for="name">Nombre: </label>
                        <input name="name" id="name" type="text" tabindex="2" />
                        <br />
                        <label for="lastname">Apellido: </label>
                        <input name="lastname" id="lastname" type="text" tabindex="2" />
                        <br />
                        <label for="phone">Telefono: </label>
                        <input name="phone" id="phone" type="text" tabindex="2" />
                        <br />
                        <label for="movil">Movil: </label>
                        <input name="movil" id="movil" type="text" tabindex="2" />
                        <br />
                        <label for="sykpe">Sykpe: </label>
                        <input name="sykpe" id="sykpe" type="text" tabindex="2" />
                        <br />
                        <legend>Información del Plataformas</legend>
                        <label for="idlkqd">ID LKQD: </label>
                        <input name="idlkqd" id="idlkqd" type="text" tabindex="2" />
                        <br />
                        <label for="ssid">ID SpringServer: </label>
                        <input name="ssid" id="ssid" type="text" tabindex="2" />
                        <br />
                        <legend>Información de Facturación</legend>
                        <label for="ef">Estado Fiscal: </label>
                        <select name="ef" id="ef" tabindex="2">
	                    <?php 
	                        $sql = "SELECT * FROM " . COUNTRIES . " ORDER BY id ASC";
							$query = $db->query($sql);
							if($db->num_rows($query) > 0){
								while($Co = $db->fetch_array($query)){
									?><option value="<?php echo $Co['id']; ?>"><?php echo $Co['country_name']; ?></option><?php
								}
							}
                        ?>
	                    </select>
                        <br />
                        <label for="nifcif">NIF/CIF: </label>
                        <input name="nifcif" id="nifcif" type="text" tabindex="2" />
                        <br />
                        <label for="company">Empresa: </label>
                        <input name="company" id="company" type="text" tabindex="2" />
                        <br />
                        <label for="country">País: </label>
                        <select name="country" id="country" tabindex="2">
	                    <?php 
	                        $sql = "SELECT * FROM " . COUNTRIES . " ORDER BY id ASC";
							$query = $db->query($sql);
							if($db->num_rows($query) > 0){
								while($Co = $db->fetch_array($query)){
									?><option value="<?php echo $Co['id']; ?>"><?php echo $Co['country_name']; ?></option><?php
								}
							}
                        ?>
	                    </select>
                        <br />
                        <label for="province">Provincia: </label>
                        <input name="province" id="province" type="text" tabindex="2" />
                        <br />
                        <label for="city">Ciudad: </label>
                        <input name="city" id="city" type="text" tabindex="2" />
                        <br />
                        <label for="cp">Código Postal: </label>
                        <input name="cp" id="cp" type="text" tabindex="2" />
                        <br />
                        <label for="address">Dirección: </label>
                        <input name="address" id="address" type="text" tabindex="2" />
                        <br />
                        <label for="currency">Moneda: </label>
                        <select name="currency" id="currency" tabindex="2" style="width:120px;">
                        	<option value="1">Dolar</option>
                        	<option value="2">Euro</option>
                        </select>
                        <br />
                      </fieldset>
                      <div align="center">
                      <input id="button1" type="submit" value="Agregar" name="agrega" /> 
                      </div>
                    </form>
                </div>
				<?php
					}else{
						$sql = "SELECT * FROM " . USERS . " WHERE id = '" . intval($_GET['edit']) . "'";
						$query = $db->query($sql);
						$Datos = $db->fetch_array($query);
				?>
				<div id="box">
                	<h3 id="adduser">Editar Usuario</h3>
					<?php if(!empty($Error)){ ?><div style="text-align:center; margin:auto; color:red; padding-top:5px;"><?php echo $Error?></div><?php } ?>
                    <form id="form" action="" method="post" enctype="multipart/form-data">
                      <fieldset id="personal">
                        <legend>Información del Usuario</legend>
                        <br/>
						<label for="user">ID: <strong><?php echo $Datos['id']; ?></strong></label>
						<br/>
						<label for="user">Usuario: </label>
						<input name="user" id="user" value="<?php echo $Datos['user']; ?>" type="text" tabindex="2" />
						<br/>
						<label for="password">Contraseña: </label>
                        <input name="password" id="password" type="password" tabindex="2" /> Dejar en blanco para no modificar
                        <br /> 
                        <label for="password2">Confirmar Contraseña: </label>
                        <input name="password2" id="password2" type="password" tabindex="2" />
                        <br /> 
						<label for="email">Email: </label>
                        <input name="email" id="email" value="<?php echo $Datos['email']; ?>" type="text" tabindex="2" />
                        <br /> 
						<label for="name">Nombre: </label>
                        <input name="name" id="name" value="<?php echo $Datos['name']; ?>" type="text" tabindex="2" />
                        <br />
                        <label for="lastname">Apellido: </label>
                        <input name="lastname" id="lastname" value="<?php echo $Datos['lastname']; ?>" type="text" tabindex="2" />
                        <br />
                        <label for="phone">Telefono: </label>
                        <input name="phone" id="phone" value="<?php echo $Datos['phone']; ?>" type="text" tabindex="2" />
                        <br />
                        <label for="movil">Movil: </label>
                        <input name="movil" id="movil" value="<?php echo $Datos['movil']; ?>" type="text" tabindex="2" />
                        <br />
                        <label for="sykpe">Sykpe: </label>
                        <input name="sykpe" id="sykpe" value="<?php echo $Datos['sykpe']; ?>" type="text" tabindex="2" />
                        <br />
                        <legend>Información del Plataformas</legend>
                        <label for="idlkqd">ID LKQD: </label>
                        <input name="idlkqd" id="idlkqd" value="<?php echo $Datos['LKQD_id']; ?>" type="text" tabindex="2" />
                        <br />
                        <label for="ssid">ID SpringServer: </label>
                        <input name="ssid" id="ssid" value="<?php echo $Datos['SS_id']; ?>" type="text" tabindex="2" />
                        <br />
                        <legend>Información de Facturación</legend>
                        <label for="ef">Estado Fiscal: </label>
                        <select name="ef" id="ef" tabindex="2">
	                    <?php 
	                        $sql = "SELECT * FROM " . COUNTRIES . " ORDER BY id ASC";
							$query = $db->query($sql);
							if($db->num_rows($query) > 0){
								while($Co = $db->fetch_array($query)){
									?><option value="<?php echo $Co['id']; ?>" <?php if($Datos['ef'] == $Co['id']){ echo ' selected="selected"';} ?>><?php echo $Co['country_name']; ?></option><?php
							}
							}
                        ?>
	                    </select>
                        <br />
                        <label for="nifcif">NIF/CIF: </label>
                        <input name="nifcif" id="nifcif" value="<?php echo $Datos['nifcif']; ?>" type="text" tabindex="2" />
                        <br />
                        <label for="company">Empresa: </label>
                        <input name="company" id="company" value="<?php echo $Datos['company']; ?>" type="text" tabindex="2" />
                        <br />
                        <label for="country">País: </label>
                        <select name="country" id="country" tabindex="2">
	                    <?php 
	                        $sql = "SELECT * FROM " . COUNTRIES . " ORDER BY id ASC";
							$query = $db->query($sql);
							if($db->num_rows($query) > 0){
								while($Co = $db->fetch_array($query)){
									?><option value="<?php echo $Co['id']; ?>" <?php if($Datos['country'] == $Co['id']){ echo ' selected="selected"';} ?>><?php echo $Co['country_name']; ?></option><?php
								}
							}
                        ?>
	                    </select>
                        <br />
                        <label for="province">Provincia: </label>
                        <input name="province" id="province" value="<?php echo $Datos['province']; ?>" type="text" tabindex="2" />
                        <br />
                        <label for="city">Ciudad: </label>
                        <input name="city" id="city" value="<?php echo $Datos['city']; ?>" type="text" tabindex="2" />
                        <br />
                        <label for="cp">Código Postal: </label>
                        <input name="cp" id="cp" value="<?php echo $Datos['cp']; ?>" type="text" tabindex="2" />
                        <br />
                        <label for="address">Dirección: </label>
                        <input name="address" id="address" value="<?php echo $Datos['address']; ?>" type="text" tabindex="2" />
                        <br />
                        <label for="currency">Moneda: </label>
                        <select name="currency" id="currency" tabindex="2" disabled="true" style="width:120px;">
                        	<option value="1">Dolar</option>
                        	<option value="2"<?php if( $Datos['currency'] == 2 ) { echo ' selected="selected"'; }  ?>>Euro</option>
                        </select>
                        <br />
                      </fieldset>
                      <div align="center">
                      <input id="button1" type="submit" value="Guardar Cambios" name="guarda" /> 
                      </div>
                    </form>
                </div>
				<?php
					}
				?>
            </div>
            <div id="sidebar">
  				     
          </div>
      </div>
        <div id="footer">
        <div id="credits">
   		Template by <a href="http://www.bloganje.com">Bloganje</a>
        </div>
        <div id="styleswitcher">
            <ul>
                <li><a href="javascript: document.cookie='theme='; window.location.reload();" title="Default" id="defswitch">d</a></li>
                <li><a href="javascript: document.cookie='theme=1'; window.location.reload();" title="Blue" id="blueswitch">b</a></li>
                <li><a href="javascript: document.cookie='theme=2'; window.location.reload();" title="Green" id="greenswitch">g</a></li>
                <li><a href="javascript: document.cookie='theme=3'; window.location.reload();" title="Brown" id="brownswitch">b</a></li>
                <li><a href="javascript: document.cookie='theme=4'; window.location.reload();" title="Mix" id="mixswitch">m</a></li>
            </ul>
        </div><br />

        </div>
</div>
</body>
</html>