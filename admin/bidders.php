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
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
		
	if(@$_SESSION['Type'] == 3){

		if(isset($_GET['d'])){
			$idDel = intval($_GET['d']);
			$sql = "UPDATE " . BIDDERS . " SET Deleted = 1 WHERE id = '$idDel' LIMIT 1";
			$db->query($sql);
		}
		
		if(isset($_GET['de'])){
			$idDe = intval($_GET['de']);
			$sql = "UPDATE " . BIDDERS . " SET Active = 0 WHERE id = '$idDe' LIMIT 1";
			$db->query($sql);
		}
		
		if(isset($_GET['a'])){
			$idA = intval($_GET['a']);
			$sql = "UPDATE " . BIDDERS . " SET Active = 1 WHERE id = '$idA' LIMIT 1";
			$db->query($sql);
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
								<div class="titl">Bidders</div>
							</div>
							<div class="bx-bd">
								<!--<Control de zonas>-->
								<div class="bx-cn bx-shnone">
									<div class="bx-bd">
										<div class="bx-pd">
											<!--<table>-->
											<div class="tbl-cn">
												<table id="tbl-estats">
													<thead>
														<tr>
															<th>Nombre</th>
															<th>Placements</th>
															<th>Agregado</th>
															<th>Opciones</th>
														</tr>
													</thead>
													<tbody><?php
													$sql = "SELECT * FROM " . BIDDERS . " WHERE Deleted = 0 ORDER BY Name ASC";
													$query = $db->query($sql);
													if($db->num_rows($query) > 0){
														while($Bidder = $db->fetch_array($query)){
															?><tr>
																<td data-title="Nombre"><?php echo $Bidder['Name']; ?></td>
																<td data-title="Placements"><?php 
																	$sql = "SELECT COUNT(id) FROM " . PLACEMENTS . " WHERE Deleted = 0 AND idBidder = '" . $Bidder['id'] . "' ";
																	echo $db->getOne($sql);	
																?></td>
																<td data-title="Agregado"><?php echo date('d.m.Y', $Bidder['Time']); ?></td>
																<td data-title="Opciones">
																	<ul class="lst-opt">
																		<li><a href="edit-bidder.php?idb=<?php echo $Bidder['id']; ?>" class="fa-edit tt-lt" data-toggle="tooltip" title="" data-original-title="Editar Bidder"></a></li><?php
																			if($Bidder['Active'] == 1){
																				?><li><a href="bidders.php?de=<?php echo $Bidder['id']; ?>" onclick="return confirm('¿Esta seguro de Desactivar este Bidder? Todos los anuncios de este Bidder serán suspendidos.')" class="fa-pause tt-lt" data-toggle="tooltip" title="" data-original-title="Desactivar Bidder"></a></li><?php
																			}else{
																				?><li><a href="bidders.php?a=<?php echo $Bidder['id']; ?>" onclick="return confirm('¿Esta seguro de activar este Bidder?')" class="fa-play-circle tt-lt" data-toggle="tooltip" title="" data-original-title="Desactivar Bidder"></a></li><?php
																			}
																		?>
																		<li><a href="bidders.php?d=<?php echo $Bidder['id']; ?>" onclick="return confirm('¿Esta seguro de eliminar este Bidder? Todos los anuncios de este Bidder serán eliminados.')" class="fa-trash-o tt-lt" data-toggle="tooltip" title="" data-original-title="Eliminar Bidder"></a></li>
																	</ul>
																</td>
															</tr><?php
														}
													}
													?>
													</tbody>
												</table>
											</div>
											<!--</table>-->
										</div>
										
										<div class="frm-group d-fx lbl-lf">
											<ul class="lst-tbs b-fx mb2" style="margin: auto; margin-bottom: 20px; margin-top: 40px;">
												<li class="b-rt"><a href="add-bidder.php" class="fa-plus-circle">Añadir Nuevo Bidder</a></li>
											</ul>
										</div>
										
										
									</div>
								</div>
								<!--</Control de zonas>-->	
						
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
    
    <script src="js/jquery.dataTables.min.js"></script>
    <script>
	    
	jQuery(document).ready(function($){
		

		
	});
	
	</script>
</body>
</html>
</html><?php
	}else{
		header('Location: index.php');
		exit(0);
	}
?>