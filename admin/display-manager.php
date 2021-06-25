<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('../config.php');
	require('../constantes.php');
	require('../db.php');
	require('../common.lib.php');
	//require('countries.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	require('libs/display.lib.php');
	
	if(@$_SESSION['Type'] == 3){
		
		if(isset($_POST['activate'])){
			$sql = "SELECT * FROM " . SITES . " WHERE deleted = 0 AND ed = 0";
			$query = $db->query($sql);
			if($db->num_rows($query) > 0){
				while($Site = $db->fetch_array($query)){
					$idSite = $Site['id'];
					$sql = "SELECT COUNT(*) FROM " . ADS . " WHERE idSite = $idSite AND Type = 3";
					if($db->getOne($sql) > 0){
						$sql = "SELECT COUNT(*) FROM " . ADS . " WHERE idSite = $idSite AND Type = 10";
						if($db->getOne($sql) == 0){
							$CCode = '{3:1}';
							$Time = time();
							$Date = date('Y-m-d');
							$sql = "INSERT INTO " . ADS . "(idSite, idSCode, idLKQD, divID, Type, Width, Height, Close, DFP, Override, HeightA, SPosition, CCode, Time, Date) 
							VALUES ('$idSite','0','AUTO','','10','0','0','0','0','0','0','', '$CCode' , '$Time','$Date')";
							$db->query($sql);
							newGenerateJS($idSite);
							//echo $idSite;
							//exit(0);
						}
					}
				}
			}
		}
		
		if(isset($_POST['desactivate'])){
			$sql = "SELECT * FROM " . SITES . " WHERE deleted = 0";
			$query = $db->query($sql);
			if($db->num_rows($query) > 0){
				while($Site = $db->fetch_array($query)){
					if($Site['filename'] != ''){
						$idSite = $Site['id'];
						$sql = "DELETE FROM " . ADS . " WHERE idLKQD = 'AUTO' AND idSite = $idSite";
						$db->query($sql);
						newGenerateJS($idSite);
						//echo $idSite . '-';
					}
				}
			}
		}
		
		if(isset($_POST['csv'])){
			$sql = "SELECT * FROM " . SITES . " WHERE deleted = 0";
			$query = $db->query($sql);
			$CSVLine = "Site ID, Name, URL \n";
			if($db->num_rows($query) > 0){
				
				while($Site = $db->fetch_array($query)){
					$idSite = $Site['id'];
					$SiteName = $Site['sitename'];
					$SiteUrl = $Site['siteurl'];
					
					$sql = "SELECT COUNT(*) FROM " . ADS . " WHERE idSite = $idSite AND idLKQD = 'AUTO'";
					if($db->getOne($sql) > 0){
						$CSVLine .=	"$idSite,$SiteName,$SiteUrl \n";
					}
				}
			}
			header('Content-Disposition: attachment; filename="sitesautoplay.csv";');
			echo $CSVLine;
			exit();
		}

?><!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Vidoomy - Display Manager</title>
    <meta name="description" content="Vidoomy">
    <meta name="keywords" content="Vidoomy">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://fonts.googleapis.com/css?family=Cabin:400italic,600italic,700italic,400,600,700' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Lato:300,400,700,900' rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="css/fa.css">
    <link rel="stylesheet" href="css/css.css">
    <link rel="stylesheet" href="css/autocomplete.css">
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
								<div class="titl">Display Manager</div>
							</div>
							<div class="bx-bd">
								<div class="bx-pd">
									<div class="bx-hd dfl b-fx">
										<div class="titl">Crear y Eliminar Anuncios Display Desktop</div>
									</div>
									<div class="clsd-fx">
										<div class="clmd12">
											<form action="" method="post" class="frm-adrsit">
												<div class="botnr-cn">
													<input type="submit" class="fa-save" value="Activar Anuncios de Display" name="save" />
													<input type="hidden" name="activate" value="1" />
												</div>
											</form>
										</div>
										<div class="clmd12">
											<form action="" method="post" class="frm-adrsit">
												<div class="botnr-cn">
													<input type="submit" class="fa-save" value="Desactivar Anuncios de Display" name="save" />
													<input type="hidden" name="desactivate" value="1" />
												</div>
											</form>
										</div>
										<div class="clmd12">
											<form action="" method="post" target="_blank" class="frm-adrsit">
												<div class="botnr-cn">
													<input type="submit" class="fa-save" value="Descargar CSV" name="save" />
													<input type="hidden" name="csv" value="1" />
												</div>
											</form>
										</div>
									</div>		
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
</body>
</html><?php
	}else{
		header('Location: index.php');
		exit(0);
	}
?>