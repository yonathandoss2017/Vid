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
		/*
		if(isset($_GET['del'])){
			if($_GET['del'] > 0){
				$idDel = intval($_GET['del']);
				$sql = "UPDATE " . SITES . " SET deleted = 1 WHERE id = '$idDel' LIMIT 1";
				$db->query($sql);
				header('Location: sites.php');
				exit(0);
			}
		}
		*/
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
								<div class="titl">Site! Add new ads for your site</div>
								<a href="add-site.php" class="btn-hd fa-plus-circle b-rt btn-nbd">Add Website</a>
							</div>
							<div class="bx-bd">
								<div class="bx-pd">
									<div class="bx-hd dfl b-fx">
										<div class="titl">Sites</div>
										<!--<div>
											<div class="fs-dropdown slct-hd srch-inp dropdown" tabindex="0">
												<input type="text" placeholder="Escribe una página para filtrar" data-toggle="dropdown">
												<div class="dropdown-menu">											
													<div class="fs-dropdown-options bx-cn">
														<span class="fs-dropdown-group">Resultados de tu busqueda</span>
														<button type="button" class="fs-dropdown-item" data-value="1"><strong>Fotosd</strong>eguarras.com</button>
														<button type="button" class="fs-dropdown-item" data-value="2"><strong>Fotosd</strong>eguarras.com</button>
													</div>
												</div>
											</div>
										</div>-->
									</div>
                                    <div class="clsd-fx">
                                    	<div class="clmd12">
                                        	<ul class="lst-pags"><?php
												$sql = "SELECT * FROM " . SITES . " WHERE idUser = '" . $_SESSION['login']  . "' AND deleted = 0 ORDER BY id DESC";
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
																<li><a href="site-code.php?siteid=<?php echo $Site['id']; ?>">See tag</a></li>
																<li><a href="edit-site.php?siteid=<?php echo $Site['id']; ?>" class="fa-edit"></a></li>
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