<?php
	session_start();
	define('CONST',1);
	if($_SESSION['Type'] == 1 || $_SESSION['Type'] == 3){
		
	}else{
		header('Location: login.php');
		exit(0);
	}
	require('../config.php');
	require('../db.php');
	require('../constantes.php');
	require('../common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	
	if(isset($_GET['idsite'])){
		$idSite = intval($_GET['idsite']);
		if($idSite > 0){
			if(isset($_GET['check'])){
				if($_GET['check'] > 0){
					checkAdsTxt($idSite);
				}
			}
			
			$sql = "SELECT * FROM " . SITES . " WHERE id = '$idSite' LIMIT 1";
			$query = $db->query($sql);
			if($db->num_rows($query) > 0){
				$Site = $db->fetch_array($query);
				$siteName = $Site['sitename'];
				$Mlines = $Site['mlines'];
				$AdstxtState = $Site['adstxt'];
				$idUser = $Site['idUser'];
				
				$Site['siteurl'];
				$Url = urlToAdstxt($Site['siteurl']);
			}else{
				header('Location: pages.php');
				exit(0);
			}
		}else{
			header('Location: pages.php');
			exit(0);
		}
	}else{
		header('Location: pages.php');
		exit(0);
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
    <link rel="stylesheet" href="css/jquery-ui.structure.min.css">
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
				
				<!--<Control de zonas>-->
				<div class="bx-cn bx-shnone">
					<div class="bx-hd dfl b-fx bghd-e">
						<div class="titl">Control Ads.txt <?php echo $siteName; ?> (<a href="<?php echo $Url; ?>" target="_blank"><?php echo $Url; ?></a>)</div>
					</div>
					<div class="bx-bd">
						<div class="bx-pd">
							<ul class="lst-tbs b-fx mb2">
								<li class="b-rt"><a href="checkads.php?idsite=<?php echo $idSite; ?>&check=1" class="fa-plus-circle">Volver a Chequear</a></li>
							</ul>
							<!--<table>-->
							<div class="tbl-cn">
								<table id="tbl-estats">
									<thead>
										<tr>
											<th>Nro</th>
											<th>Línea</th>
											<th>Estado</th>
										</tr>
									</thead>
									<tbody><?php
										
									if($AdstxtState == 2){
										?><tr><td>Archivo Ads.txt no encontrado.</td></tr><?php
									}else{
									
										$AMlines = explode(',',$Mlines);
										
										$sql = "SELECT LKQD_id FROM " . USERS . " WHERE id = '$idUser' LIMIT 1";
										$idLkqd = $db->getOne($sql);
										
										$sql = "SELECT * FROM " . ADSTXT . " ORDER BY id ASC";
										$query = $db->query($sql);
										$N = 0;
										if($db->num_rows($query) > 0){
											while($Adstxt = $db->fetch_array($query)){
												$LineTxt = str_replace('{LKQDID}', $idLkqd, $Adstxt['LineTxt']);
												//echo $LineTxt . ' - ';
												$N++;
												?><tr>
													<td data-title="Nro"><?php echo $N; ?></td>
													<td data-title="Linea"><?php echo $LineTxt; ?></td>
													<td data-title="Estado" style="text-align:center;"><?php
														if(!in_array($Adstxt['id'], $AMlines)){
															?><span class="fa-check-circle"></span><?php
														}else{
															?><span class="fa-times-circle"></span><?php
														}
														?>
													</td>
												</tr><?php
											}
										}
										
									}
									?>
									</tbody>
								</table>
							</div>
							<!--</table>-->
						</div>
						
					</div>
				</div>
				<!--</Control de zonas>-->						
				
			</div>
		</div>
		<!--</bdcn>-->
		
		<?php include 'footer.php'; ?>
		
	</div>
	<!--</all>-->

    <!-- Javascript -->
    <script src="js/lib/jquery.js"></script>
    <script src="js/lib/jquery-ui.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-filestyle.js"></script>
    
	<!-- Tables -->
	<script src="js/jquery.dataTables.min.js"></script>
	<script>
		/*
		jQuery(document).ready(function($){
            $( "#tabs" ).tabs();
			$('#tbl-estats').dataTable({
				"paging": false,
				"lengthChange": false,
				"searching": false,
				"info":     false
    		});
		});
		*/
	</script>
	
</body>
</html>