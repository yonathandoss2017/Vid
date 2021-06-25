<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('config.php');
	require('constantes.php');
	require('db.php');
	require('common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	if(@$_SESSION['login'] >= 1){
		$idPub = $_SESSION['login'];
		if(isset($_GET['siteid'])){
			$siteId = intval($_GET['siteid']);
			if($siteId > 0){
				if($idPub == 1257){
					$sql = "SELECT * FROM " . SITES . " WHERE id = '$siteId' AND (idUser = '$idPub' OR idUser = 64) LIMIT 1";
				}else{
					$sql = "SELECT * FROM " . SITES . " WHERE id = '$siteId' AND idUser = '$idPub' LIMIT 1";
				}
				$query = $db->query($sql);
				if($db->num_rows($query) > 0){
					$siteData = $db->fetch_array($query);
					
					$sitename = $siteData['sitename'];
					$siteurl = $siteData['siteurl'];
					$nameError = '';
					$nameErrorc = '';
					$nameErrors = '';
				
					$sel[1] = '';
					$sel[2] = '';
					$sel[3] = '';
					$sel[4] = '';
					$sel[10] = '';
					$sel[$siteData['category']] = ' checked="checked"';

					
					if(isset($_POST['save'])){
						$sitename = my_clean($_POST['sitename']);
						$category = intval($_POST['category']);
						$sel[$siteData['category']] = '';
						$sel[$category] = ' checked="checked"';
						if($_POST['save'] != ''){
							if($_POST['sitename'] != ''){
								$sql = "UPDATE " . SITES . " SET sitename = '$sitename', category = '$category' WHERE id = '$siteId' AND idUser = '$idPub' LIMIT 1";
								$db->query($sql);
							}else{
								$nameError = ' data-error="Debe completar el Nombre del sitio web."';
								$nameErrorc = ' frm-rrr';
								$nameErrors = ' style="margin-bottom:20px;"';
							}
						}
					}
		
?><!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Vidoomy</title>
    <meta name="description" content="Vidoomy">
    <meta name="keywords" content="Vidoomy">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://fonts.googleapis.com/css?family=Cabin:400italic,600italic,700italic,400,600,700' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Lato:300,400,700,900' rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="css/fa.css">
    <link rel="stylesheet" href="css/css.css">
    <link rel="icon" type="image/png" href="img/favicon.png">
</head>
<body>

	<!--<all>-->
	<div class="all">
	
		<?php include 'header.php'; ?>
		
		<!--<bdcn>-->
		<div class="bdcn">
			<div class="cnt">
				<!--<cls>-->
				<div class="cls c-fx">
                                        
					<!--<main>-->
					<main class="clmc12 c-flx1">
						<!--<Estadisticas Avanzadas>-->
						<div class="bx-cn bx-shnone">
							<div class="bx-hd dfl b-fx">
								<div class="titl">Editar Sitio Web</div>
							</div>
							<div class="bx-bd">
								<div class="bx-pd">
									<form action="" method="post" class="frm-adrsit">
										<div class="bx-hd dfl b-fx">
											<div class="titl">Editar Página</div>
										</div>
										<div class="clsd-fx">
											<div class="clmd06">
												<!--<Nombre Web>-->
												<div class="frm-group d-fx lbl-lf<?php echo $nameErrorc; ?>">
													<label<?php echo $nameErrors; ?>>Nombre Web</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $nameError; ?>>
															<input type="text" name="sitename" value="<?php echo $sitename; ?>" />
															<span class="fa-info tt-lt" data-toggle="tooltip" title="Rellena este campo con el nombre del sitio web"></span>
														</label>
													</div>
												</div>
												<!--</Nombre Web>-->
												<!--<URL>-->
												<div class="frm-group d-fx lbl-lf<?php echo $urlErrorc; ?>">
													<label<?php echo $urlErrors; ?>>URL</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $urlError; ?>>
															<input type="text" name="siteurl" value="<?php echo $siteurl; ?>" disabled="disabled" />
															<span class="fa-info tt-lt" data-toggle="tooltip" title="Rellena este campo con la URL de tu sitio web"></span>
														</label>
													</div>
												</div>
												<!--</URL>-->
											</div>
											<div class="clmd06">
												<div class="frm-nln">Tipo de contenido 
													<label class="option"><input name="category" type="radio" value="1"<?php echo $sel[1]; ?>> Periódico </label>
													<label class="option"><input name="category" type="radio" value="2"<?php echo $sel[2]; ?>> TV online </label>
													<label class="option"><input name="category" type="radio" value="3"<?php echo $sel[3]; ?>> Viral </label>
													<label class="option"><input name="category" type="radio" value="4"<?php echo $sel[4]; ?>> Juegos </label>
													<label class="option"><input name="category" type="radio" value="10"<?php echo $sel[10]; ?>> Otros</label>
												</div>
												<div class="tpcnw">
													<p>Selecciona correctamente el contenido de tu página porque te recordamos que la publicidad depende del tipo de contenido.</p>
												</div>
											</div>
										</div>
										<div class="botnr-cn">
											<input type="submit" class="fa-save" value="Guardar Cambios" name="save" /> <button class="fa-times-circle cancelar" onclick="location.href='sites.php'">Cancerlar</button>
										</div>
									</form>
								</div>
							</div>
						</div>
						<!--</Estadisticas Avanzadas>-->
					</main>
					<!--<main>-->
					
				</div>
				<!--</cls>-->
			</div>
		</div>
		<!--</bdcn>-->
		
		<?php include 'footer.php'; ?>
			
	</div>
	<!--</all>-->
    
    <!-- Javascript -->
    <script src="js/lib/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-filestyle.js"></script>
	
</body>
</html>
</html><?php
				}else{
					header('Location: sites.php');
					exit(0);
				}
			}else{
				header('Location: sites.php');
				exit(0);
			}
		}else{
			header('Location: sites.php');
			exit(0);
		}
	}else{
		header('Location: index.php');
		exit(0);
	}
?>