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
		
	if($_SESSION['Type'] == 3){
		$Default = false;
		$AndDef = '';

		if(isset($_GET['default'])){
			$Default = true;
			$AndDef = '&default=1';
		}
		
		if(isset($_GET['d'])){
			$idDel = intval($_GET['d']);
			$sql = "UPDATE " . ADUNITS . " SET Deleted = 1 WHERE id = '$idDel' LIMIT 1";
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
								<div class="titl"><?php if ($Default) { echo 'Default '; } ?>Ad Units</div>
								
								
								<div class="d-rt a-o2" style="margin: 3px 3px 0 auto;">
									<div class="fs-dropdown slct-hd dropdown" tabindex="0">
										<button type="button" class="fs-dropdown-selected fs-touch-element" data-toggle="dropdown"><?php if(isset($_GET['default'])){ echo "Default Ad Units"; } else { echo "Ad Units"; } ?></button>
										<div class="dropdown-menu">											
											<div class="fs-dropdown-options bx-cn">
												<span class="fs-dropdown-group">Seleccionar</span>
												<button type="button" class="fs-dropdown-item" onclick="location.href='ad-units.php'">Ad Units</button>
												<button type="button" class="fs-dropdown-item" onclick="location.href='ad-units.php?default=1'">Default Ad Units</button>
											</div>
										</div>
									</div>
								</div>
								
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
															<th>Tamaño</th>
															<th>Posición</th>
															<th>Agregado</th>
															<th>Opciones</th>
														</tr>
													</thead>
													<tbody><?php
													if($Default){
														$sql = "SELECT * FROM " . ADUNITS . " WHERE isDefault = 1 AND Deleted = 0 ORDER BY Name ASC";
													}else{
														$sql = "SELECT * FROM " . ADUNITS . " WHERE isDefault = 0 AND Deleted = 0 ORDER BY Name ASC";
													}
													$query = $db->query($sql);
													if($db->num_rows($query) > 0){
														while($AdUnit = $db->fetch_array($query)){
															$sql = "SELECT Size FROM " . DSIZES . " WHERE id = '" . $AdUnit['Size'] . "' LIMIT 1";
															$Size = $db->getOne($sql);
															
															$sql = "SELECT Position FROM " . DPOSITIONS . " WHERE id = '" . $AdUnit['Position'] . "' LIMIT 1";
															$Position = $db->getOne($sql);
															?><tr>
																<td data-title="Nombre"><?php echo $AdUnit['Name']; ?></td>
																<td data-title="Tamano"><?php echo $Size; ?></td>
																<td data-title="Posicion"><?php echo $Position; ?></td>
																<td data-title="Agregado"><?php echo date('d.m.Y', $AdUnit['Time']); ?></td>
																<td data-title="Opciones">
																	<ul class="lst-opt">
																		<li><a href="edit-ad-unit.php?ida=<?php echo $AdUnit['id']; ?>" class="fa-edit tt-lt" data-toggle="tooltip" title="" data-original-title="Editar Ad Unit"></a></li>
																		<li><a href="ad-units.php?d=<?php echo $AdUnit['id']; ?><?php echo $AndDef; ?>" onclick="return confirm('¿Esta seguro de eliminar este Ad Unit?')" class="fa-trash-o tt-lt" data-toggle="tooltip" title="" data-original-title="Eliminar Ad Unit"></a></li>
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
												<li class="b-rt"><a href="add-ad-unit.php<?php if ($Default) { echo '?def=1'; } ?>" class="fa-plus-circle">Añadir Nuevo<?php if ($Default) { echo ' Default'; } ?> Ad Unit</a></li>
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