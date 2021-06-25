<?
	session_start();
	define('CONST',1);
	if($_POST['user']=="admin" && $_POST['pass']=="123456"){
		$_SESSION['Admin'] = 1;
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
	$config = sacarDatosConfig();
	if(!empty($_POST['agrega'])){
		if(!empty($_POST['tipo'])){
			
			for($i = 1; $i <= 10; $i++){
				if(!empty($_FILES['imagen1_' . $i]['name'])){
					$ext = get_ext($_FILES['imagen1_' . $i]['name']);
					if(in_array($ext, $allow_types)){
						$NombreI = $_FILES['imagen1_' . $i]['name'];
						$NombreOr = '';
						$NombreDet = '';
						$Nombre = str_replace('.'.$ext,'',$_FILES['imagen1_' . $i]['name']);
						if(copy($_FILES['imagen1_' . $i]['tmp_name'],'../productos/' . $NombreI)){
							copy($_FILES['imagen1_' . $i]['tmp_name'],'../productos/thumbs/' . $NombreI);
							@generarThumbnail('../productos/thumbs/' . $NombreI,'93','63');
							if(!empty($_FILES['imagen2_' . $i]['name'])){
								$NombreDet = $_FILES['imagen2_' . $i]['name'];
								if(copy($_FILES['imagen2_' . $i]['tmp_name'],'../productos/' . $NombreDet)){
									$NombreOr = $Nombre . '_O.' . $ext;
									copy($_FILES['imagen1_' . $i]['tmp_name'],'../productos/' . $NombreOr);
									
									$fondo = imagecreatefromjpeg('../productos/' . $NombreI);
									$texto = imagecreatefromjpeg('../productos/' . $NombreDet);

									$fondoAncho = imagesx($fondo);
									$fondoAlto = imagesy($fondo);
									$textoAncho = imagesx($texto);
									$textoAlto = imagesy($texto);

									imagecopy($fondo,$texto,$fondoAncho - $textoAncho,$fondoAlto - $textoAlto,0,0,$textoAncho,$textoAlto);
									imagejpeg($fondo,'../productos/' . $NombreI);

									imagedestroy($fondo);
									imagedestroy($texto);
								}
							}
							
							$Descripcion = my_clean($_POST['descripcion_' . $i],true);
							$Tipo = intval($_POST['tipo']);
							$Temporada = intval($_POST['temporada']);
							
							$sql = "INSERT INTO " . TABLA_PRODUCTOS . " (idCategoria, Temporada, Nombre, Descripcion, Imagen, Original, Detalle, Thumb, Fecha) 
							VALUES ('$Tipo', '$Temporada', '$Nombre', '$Descripcion', '$NombreI', '$NombreOr', '$NombreDet', '$NombreI','" . date('Y-m-d') . "')";
							$db->query($sql);
							$exito = 1;
						}
					}else{$Error[$i] = "Tipo de Imagen Ivalido.";}
				}else{$Error[$i] = "Debe completar la Imagen Principal.";}
			}
		}else{$Error = "Debe completar el Tipo.";}
	}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Liztyle - Admin Area</title>
<link rel="stylesheet" type="text/css" href="css/theme.css" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
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
</script>
<!--[if IE]>
<link rel="stylesheet" type="text/css" href="css/ie-sucks.css" />
<![endif]-->
</head>

<body>
	<div id="container">
    	<div id="header">
        	<h2>Liztyle - Admin Area</h2>
    <div id="topmenu">
            	<ul>
                	<li class="current"><a href="index.php">Productos</a></li>
                    <li><a href="config.php">Configuración General</a></li>
              </ul>
          </div>
      </div>
        <div id="top-panel">
            <div id="panel">
                <ul>
					<li><a href="index.php" class="useradd">Ver/Editar Productos</a></li>
                </ul>
            </div>
      </div>
        <div id="wrapper">
            <div id="content">
                <div id="box">
                	<h3 id="adduser">Agregar Varios Productos</h3>
					<?if(!empty($Error)){?><div style="text-align:center; margin:auto; color:red; padding-top:5px;"><?
						if(is_array($Error)){
							$Br = '';
							foreach($Error as $K=>$Er){
								echo 'Error ' . $K . ': ' . $Er . $Br;
								$Br = '<br/>';
							}
						}else{
							echo $Error;
						}
						?></div><?}?>
                    <form id="form" action="indexv.php" method="post" enctype="multipart/form-data">
                      <fieldset id="personal">
                        <legend>Información del Producto</legend>
                        <label for="tipo">Tipo: </label>
                        <select name="tipo" tabindex="2"><?
							$sql = "SELECT * FROM " . TABLA_CATEGORIAS . " ORDER BY id ASC";
							$query = $db->query($sql);
							while($Tipo = $db->fetch_array($query)){
								?><option value="<?=$Tipo['id']?>"><?=$Tipo['Nombre']?></option><?
							}
						?></select>
                        <br />
						<label for="temporada">Temporada: </label>
						<select name="temporada" tabindex="2">
							<option value="1">Oto&ntilde;o Invierno</option>
							<option value="2" <?if($config['Temporada']==2){echo 'selected';}?>>Primavera Verano</option>
						</select>
                        <br />
					   </fieldset>
					
					  <?
					  for($i = 1; $i <= 10; $i++){
					  ?><fieldset id="personal">
						<legend>Producto <?=$i?></legend>
						<label for="descripcion_<?=$i?>">Descripci&oacute;n: </label>
                        <input name="descripcion_<?=$i?>" id="descripcion_<?=$i?>" type="text" tabindex="2" />
                        <br />
						<label for="imagen1_<?=$i?>">Imagen Principal: </label>
                        <input name="imagen1_<?=$i?>" id="imagen1_<?=$i?>" type="file" style="width:240px;" tabindex="2" />
						<br />
						<label for="imagen2_<?=$i?>">Imagen Detalle: </label>
                        <input name="imagen2_<?=$i?>" id="imagen2_<?=$i?>" type="file" style="width:240px;" tabindex="2" />
						<br />
                      </fieldset><?
					  }
					  ?>
					  
                      <div align="center">
                      <input id="button1" type="submit" value="Agregar" name="agrega" /> 
                      <input id="button2" type="reset" />
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
