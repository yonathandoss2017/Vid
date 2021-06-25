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
		
		/*
			if(isset($_GET['del'])){
			if($_GET['del'] > 0){
				$idDel = intval($_GET['del']);
				$sql = "UPDATE " . SITES . " SET deleted = 1 WHERE id = '$idDel' AND idUser = '$idPub' LIMIT 1";
				$db->query($sql);
				header('Location: sites.php');
				exit(0);
			}
		}
		*/
?><!doctype html>
<html lang="es">
<head>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-KDK6GTQ');</script>
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
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KDK6GTQ"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
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
								<div class="titl">Sitios! Crea tus publicidades para Páginas web</div>
								<a href="add-site.php" class="btn-hd fa-plus-circle b-rt btn-nbd">Añadir Sitio</a>
							</div>
							<div class="bx-bd">
								<div class="bx-pd">
									<div class="bx-hd dfl b-fx">
										<div class="titl">Listado de Sitios</div>
									</div>
                                    <div class="clsd-fx">
                                    	<div class="clmd12">
                                        	<ul class="lst-pags"><?php
												if($idPub == 1257){
													$sql = "SELECT * FROM " . SITES . " WHERE (idUser = '$idPub' OR idUser = '64') AND deleted = 0 AND aproved = 0 ORDER BY id DESC";
												}else{
													$sql = "SELECT * FROM " . SITES . " WHERE idUser = '$idPub' AND deleted = 0 AND aproved = 0 ORDER BY id DESC";
												}
												$query = $db->query($sql);
												if(@$db->num_rows($query) > 0){
													while($Site = $db->fetch_array($query)){
														?><li class="b-fx">
															<div class="pags-cn 0-fx b-flx1">
																<figure><?php
																	if($Site['image'] != ''){
																
																		?><img src="data:image/png;base64, <?php echo $Site['image']; ?>" alt=""><?php
																	}else{
																		?><img src="img/site.png" alt=""><?php
																	}
																	?></figure>
																<div class="0-flx1">
																	<p><a href="<?php echo $Site['siteurl']; ?>" target="_blank"><?php echo $Site['sitename']; ?></a><!-- <span>6 Zonas creadas</span>--></p>
																	<p><a href="<?php echo $Site['siteurl']; ?>" target="_blank"><?php echo $Site['siteurl']; ?></a></p>
																</div>
															</div>
															<ul class="lst-tbs 0-fx b-rt ctr">
																<li><a href="site-code.php?siteid=<?php echo $Site['id']; ?>">Ver Tag</a></li>
																<li><a href="edit-site.php?siteid=<?php echo $Site['id']; ?>" class="fa-edit"></a></li>
																<!--<li><a href="sites.php?del=<?php echo $Site['id']; ?>"  onclick="return confirm('¿Esta seguro de eliminar este sitio?')" class="fa-trash-o"></a></li>-->
															</ul>
														</li><?php
													}
												}
												?>
											</ul>
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
    <script src="js/bootstrap-filestyle.js"></script>
	
</body>
</html><?php
	}else{
		header('Location: index.php');
		exit(0);
	}
?>