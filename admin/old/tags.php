<?php
	session_start();
	define('CONST',1);
	if($_SESSION['Admin']!=1){
		header('Location: login.php');
		exit(0);
	}
	require('../config.php');
	require('../db.php');
	require('../constantes.php');
	require('../common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);

	$idUser = intval($_GET['edit']);
	if($idUser == 0){
		header('Location: index.php');
		exit(0);
	}
	
	if($_GET['del'] > 0){
		$idDel = intval($_GET['del']);
		$sql = "DELETE FROM " . TAGS . " WHERE id = '$idDel' LIMIT 1";
		$db->query($sql);
		header('Location: tags.php?edit=' . $idUser);
		exit(0);
	}
	
	if(!empty($_POST['agrega'])){
		if(!empty($_POST['zona'])){
			if(!empty($_POST['identify'])){
				if(!empty($_POST['price'])){
					
					$zona = my_clean($_POST['zona']);
					$identify = my_clean($_POST['identify']);
					$platformtype = intval($_POST['platformtype']);
					$revenue = intval($_POST['revenue']);
					$price = my_clean($_POST['price']);
					$idplatform = my_clean($_POST['idplatform']);
					
					$sql = "SELECT id FROM " . TAGS . " WHERE idTag = '$identify' AND idPlatform = '$idplatform' LIMIT 1";
					if($db->getOne($sql) == 0){
						$time = time();
						$date = date('Y-m-d');
						
						$sql = "INSERT INTO " . TAGS . " (idUser, idPlatform, PlatformType, idTag, TagName, RevenueType, Revenue,  time, date) VALUES ('$idUser', '$idplatform','$platformtype', '$identify', '$zona', '$revenue', '$price', '$time', '$date')";
						$db->query($sql);
						$exito = 1;
					}else{$Error = "Ese identificador ya existe.";}
				}else{$Error = "Debe completar el Precio.";}
			}else{$Error = "Debe completar el Identificador.";}
		}else{$Error = "Debe completar la Zona.";}
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
				<div id="box">
					<h3>Tags</h3>
					<div></div>

					<form action="" method="post" name="lista" id="lista">
                	<table width="100%">
						<thead>
							<tr>
                            	<th><input type="checkbox" onchange="jqCheckAll('lista', 'borra', this.checked);"></th>
								<th width="40px"><a href="#">ID<img src="img/icons/arrow_down_mini.gif" width="16" height="16" align="absmiddle" /></a></th>
								<th><a href="#">Nombre</a></th>
								<th width="90px"><a href="#">Plataforma</a></th>
								<th width="90px"><a href="#">Identificador</a></th>
								<th width="90px"><a href="#">Precio</a></th>
								<th width="90px"><a href="#">Plataforma</a></th>
                                <th width="80px"><a href="#">Acciones</a></th>
                            </tr>
						</thead>
						<tbody><?php
							$sql = "SELECT * FROM " . TAGS . " WHERE idUser = '$idUser' ORDER BY id DESC";
							$query = $db->query($sql);
							while($Tag = $db->fetch_array($query)){
								?><tr>
									<td class="a-center"><input type="checkbox" value="<?php echo $Tag['id']; ?>" name="borra[]" /></td>
									<td class="a-center"><?php echo $Tag['id']; ?></td>
									<td><?php echo $Tag['TagName']; ?></td>
									<td class="a-center"><?php if($Tag['idPlatform'] == 1){
										echo 'LKQD';
									}else{ 
										echo 'SpringServe';
									} ?></td>
									<td class="a-center"><?php echo $Tag['idTag']; ?></td>
									<td class="a-center"><?php 
										if($Tag['RevenueType'] == 1){
											echo 'Revenue Share: ' . $Tag['Revenue']  . '%';
										}else{
											echo 'CPM Fijo: ' . $Tag['Revenue'] . '$';
										}
									?></td>
									<td class="a-center"><?php if($Tag['PlatformType'] == 1){
										echo 'Desktop';
									}elseif($Tag['PlatformType'] == 2){ 
										echo 'Mobile Web';
									}elseif($Tag['PlatformType'] == 3){ 
										echo 'Mobile App';
									}elseif($Tag['PlatformType'] == 4){ 
										echo 'CTV';
									} ?></td>
									<td class="a-center">
										<a onclick="return confirm('¿Esta seguro de que desea ELIMINAR este TAG?')" href="tags.php?edit=<?php echo $idUser; ?>&del=<?php echo $Tag['id']; ?>" title="Eliminar"><img src="img/icons/cancel.png" alt="Eliminiar" width="16" height="16" /></a>
									</td>
								</tr>
							<?php
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
                	<h3 id="adduser">Asiganar Tag</h3>
					<?php if(!empty($Error)){ ?><div style="text-align:center; margin:auto; color:red; padding-top:5px;"> <?php echo$Error; ?></div><?php } ?>
                    <form id="form" action="" method="post" enctype="multipart/form-data">
                      <fieldset id="personal">
                        <!--<label for="user">Usuario: </label>
                        <select name="user" id="user" tabindex="2">
                        <?php 
	                        $sql = "SELECT * FROM " . USERS . " ORDER BY id DESC";
							$query = $db->query($sql);
							while($User = $db->fetch_array($query)){
								?><option value="<?php echo $User['id']; ?>"><?php echo $User['user']; ?></option><?php
							}
                        ?>
                        </select>
                        <br />-->
                        <label for="zona">Zona: </label>
                        <input name="zona" id="zona" type="text" tabindex="2" />
                        <br /> 
                        <label for="platformtype">Plataforma: </label>
                        <select name="platformtype" id="platformtype" tabindex="2">
                        	<option value="1">Desktop</option>
                        	<option value="2">Mobile Web</option>
                        	<option value="3">Mobile App</option>
                        	<option value="4">CTV</option>
                        </select>
                        <br /> 
                        <label for="revenue">Precio: </label>
                        <select name="revenue" id="revenue" tabindex="2" style="width:120px;">
                        	<option value="1">Revenue Share</option>
                        	<option value="2">CPM Fijo</option>
                        </select>
                        <input name="price" id="price" type="text" tabindex="2" style="width:60px;" />
                        <br /> 
						<label for="idplatform">Plataforma: </label>
                        <select name="idplatform" id="idplatform" tabindex="2">
                        	<option value="1">LKQD</option>
                        	<option value="2">SpringServe</option>
                        	<!--<option value="3">Ooyala</option>-->
                        </select>
                        <br /> 
                        <label for="identify">Identificador: </label>
                        <input name="identify" id="identify" type="text" tabindex="2" />
                        <br />
                      </fieldset>
                      <div align="center">
                      <input id="button1" type="submit" value="Asiganar" name="agrega" /> 
                      </div>
                    </form>
                </div>
				
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