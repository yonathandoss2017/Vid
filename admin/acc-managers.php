<?php
	session_start();
	define('CONST',1);
	if($_SESSION['Type'] != 3){
		header('Location: index.php');
		exit(0);
	}
	require('../config.php');
	require('../db.php');
	require('../constantes.php');
	require('../common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
		
	if(isset($_GET['del'])){
		if($_GET['del'] > 0){
			$idDel = intval($_GET['del']);
			if($idDel != 1){
				$sql = "UPDATE " . ACC_MANAGERS . " SET Deleted = 1 WHERE id = '$idDel' LIMIT 1";
				$db->query($sql);
			}
			header('Location: acc-managers.php');
			exit(0);
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
						<div class="titl">Account Managers</div>
					</div>
					<div class="bx-bd">
						<div class="bx-pd">
							<ul class="lst-tbs b-fx mb2">
								<li class=""><a href="edit-objetive.php?ido=1" class="fa-crosshairs">Editar Objetivo Global por Defecto</a></li>
								<li class="b-rt"><a href="add-acc-managers.php" class="fa-plus-circle">Añadir Account Manager</a></li>
							</ul>
							<!--<table>-->
							<div class="tbl-cn">
								<table id="tbl-estats">
									<thead>
										<tr>
											<th>Nombre</th>
											<th>E-Mail</th>
											<th>Tipo</th>
											<th>Último Login</th>
											<th>IP</th>
											<th>Opciones</th>
										</tr>
									</thead>
									<!--<tfoot>
										<tr>
											<th colspan="7">
												<span>Páginas creadas: <strong>28</strong></span> <span class="txspr">·</span> <span>Zonas Creadas: <strong>242</strong></span> <span class="txspr">·</span> <span>Formato más productivo: <strong>Intersitial</strong></span> <span class="txspr">·</span> <span>Página web más productiva: <strong>Miscojones.com</strong></span>
											</th>
										</tr>
									</tfoot>-->
									<tbody><?php
									$sql = "SELECT * FROM " . ACC_MANAGERS . " WHERE Deleted = 0 ORDER BY id DESC";
									$query = $db->query($sql);
									if($db->num_rows($query) > 0){
										while($Acc = $db->fetch_array($query)){
											?><tr>
												<td data-title="Nombre"><?php echo $Acc['Name']; ?></td>
												<td data-title="URL"><?php echo $Acc['Email']; ?></td>
												<td data-title="Tipo"><?php if($Acc['Type'] == 3){ echo 'Admin'; } elseif($Acc['Type'] == 2) { echo 'Advertisers'; } else { echo 'Publishers'; } ; ?></td>
												<td data-title="Login"><?php if($Acc['LastLogin'] != 0) { echo date('H:i:s d/m/Y', $Acc['LastLogin']); } else { echo 'NA'; } ?></td>
												<td data-title="IP"><?php echo $Acc['IP']; ?></td>
												<td data-title="Opciones">
													<ul class="lst-opt">
														<li><a href="edit-acc-managers.php?iduser=<?php echo $Acc['id']; ?>" class="fa-edit tt-lt" data-toggle="tooltip" title="" data-original-title="Editar Account Manager"></a></li><?php
														if($Acc['id'] != 1 && $Acc['id'] != 15){
														?><li><a href="acc-managers.php?del=<?php echo $Acc['id']; ?><?php echo $linkAnd; ?>" onclick="return confirm('¿Esta seguro de eliminar este Account Manager? La misma no podrá ser recuperada')" class="fa-trash-o tt-lt" data-toggle="tooltip" title="" data-original-title="Eliminar Account Manager"></a></li><?php
														}
														if($Acc['Type'] == 1){
															?><li><a href="objetives.php?idam=<?php echo $Acc['id']; ?>" class="fa-binoculars tt-lt" data-toggle="tooltip" title="" data-original-title="Ver Cumplimiento de Objetivos"></a></li>
															<li><a href="objetives-manager.php?idam=<?php echo $Acc['id']; ?>" class="fa-crosshairs tt-lt" data-toggle="tooltip" title="" data-original-title="Editar Objetivos"></a></li><?php
														}
													?></ul>
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
		jQuery(document).ready(function($){
                        $( "#tabs" ).tabs();
			$('#tbl-estats').dataTable({
				"paging": false,
				"lengthChange": false,
				"searching": false,
				"info":     false
    		});
		});
	</script>
	
</body>
</html>