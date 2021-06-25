<?php
	session_start();
	define('CONST',1);
	require('../config.php');
	require('../db.php');
	require('../constantes.php');
	require('../common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);

	if($_SESSION['Admin']!=1){
		header('Location: login.php');
		exit(0);
	}elseif($_SESSION['Type'] != 3){
		header('Location: index.php');
		exit(0);	
	}

	if(isset($_GET['del'])){
		if($_GET['del'] > 0){
			$idDel = intval($_GET['del']);
			//$sql = "DELETE FROM " . USERS . " WHERE id = '$idDel' LIMIT 1";
			$sql = "UPDATE " . USERS . " SET deleted = 1 WHERE id = '$idDel' AND type = 0 LIMIT 1";
			$db->query($sql);
			header('Location: confirm.php?den=1');
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
						<div class="titl">Control de Publishers</div>
						
						<?php if($_SESSION['Type'] == 3){ ?>
						<div class="d-rt a-o2" style="margin: 3px 3px 0 auto;">
							<div class="fs-dropdown slct-hd dropdown" tabindex="0">
								<button type="button" class="fs-dropdown-selected fs-touch-element" data-toggle="dropdown"><?php if(isset($_GET['int'])){ echo "Pendientes de Integración"; } elseif(isset($_GET['den'])) { echo "Denegados"; } else { echo "Pendientes de Aprobación"; } ?></button>
								<div class="dropdown-menu">											
									<div class="fs-dropdown-options bx-cn">
										<span class="fs-dropdown-group">Seleccionar</span>
										<button type="button" class="fs-dropdown-item" onclick="location.href='index.php'">Alta Manual</button>
										<button type="button" class="fs-dropdown-item" onclick="location.href='confirm.php'">Pendientes de Aprobación (<?php
										$sql = "SELECT COUNT(id) FROM " . USERS . " WHERE AccM = 15 AND integrate = 1";
										echo $db->getOne($sql);
										?>)</button>
										<button type="button" class="fs-dropdown-item" onclick="location.href='confirm.php?int=1'">Pendientes de Integración (<?php
										$sql = "SELECT COUNT(id) FROM " . USERS . " WHERE AccM = 15 AND integrate = 0";
										echo $db->getOne($sql);
										?>)</button>
										<button type="button" class="fs-dropdown-item" onclick="location.href='index.php?a=1'">Aceptados</button>
										<button type="button" class="fs-dropdown-item" onclick="location.href='confirm.php?den=1'">Denegados</button>
									</div>
								</div>
							</div>
						</div>
						<?php } ?>
					</div>
					<div class="bx-bd">
						<div class="bx-pd">
							<ul class="lst-tbs b-fx mb2">
								<?php if($_SESSION['Type'] == 3){ ?>
								<li class="b-lt"><a href="statsviews.php" class="fa-signal" style="display:inline-block;">Estad&iacute;sticas Publishers Pendientes</a></li>
								<?php } ?>
								<li class="b-rt"><a href="add-user.php" class="fa-plus-circle" style="display:inline-block;">Añadir Publisher</a></li>
							</ul>					
							<!--<table>-->
							<div class="tbl-cn">
								<table id="tbl-estats">
									<thead>
										<tr>
											<th>ID</th>
											<th>Nombre</th>
											<th>Telefono</th>
											<th>Skype</th>
											<th>E-Mail</th>
											<th>Agregado</th>
											<th>Mail OK</th>
											<th>Opciones</th>
										</tr>
									</thead>
									
									<tbody><?php
									if(isset($_GET['den'])){
										$AccMS = 9999;
									}else{
										$AccMS = 15;
									}
									if(isset($_GET['int'])){
										$Inte = 0;
									}else{
										$Inte = 1;
									}
									$sql = "SELECT * FROM " . USERS . " WHERE deleted = 0 AND type = 0 AND AccM = $AccMS AND integrate = $Inte ORDER BY user ASC";
									$query = $db->query($sql);
									if($db->num_rows($query) > 0){
										while($User = $db->fetch_array($query)){
											$aR = explode('-',$User['date']);
											$Fecha = $aR[2] . '/' . $aR[1] . '/' . $aR[0];
											?><tr>
												<td data-title="ID"><?php echo $User['id']; ?></td>
												<td data-title="Nombre"><?php echo $User['name']; ?> <?php echo $User['lastname']; ?></td>
												
												<td data-title="Telefono">+<?php echo $User['phone']; ?></td>
												<td data-title="Skype"><?php echo $User['sykpe']; ?></td>
												<td data-title="Email"><?php echo $User['email']; ?></td>
												<td data-title="Agregado"><?php echo $Fecha; ?></td>
												<td data-title="Ingreso"><?php if($User['verified'] == 1) { echo '<span style="color:red;">No</span>'; } else { echo '<span style="color:green;">Verificado</span>'; } ?></td>
												<td data-title="Opciones" >
													<ul class="lst-opt"> <?php
														if($AccMS == 15){
														?>
														
														<li><a href="aprove.php?iduser=<?php echo $User['id']; ?>" class="fa-check-square tt-lt" data-toggle="tooltip" title="" data-original-title="Aprobar Publisher"></a></li>
														<li><a href="edit-user.php?iduser=<?php echo $User['id']; ?>" class="fa-edit tt-lt" data-toggle="tooltip" title="" data-original-title="Editar Publisher"></a></li>
														<!--<li><a href="pages.php?iduser=<?php echo $User['id']; ?>" class="fa-eye tt-lt" data-toggle="tooltip" title="" data-original-title="Ver/Editar Paginas"></a></li>-->
														<li><a href="nastats.php?iduser=<?php echo $User['id']; ?>" class="fa-signal tt-lt" data-toggle="tooltip" title="" data-original-title="Ver Estad&iacute;sticas"></a></li>
														<?php
														}else{
														?>
														<li><a href="confirm.php?del=<?php echo $User['id']; ?>" onclick="return confirm('¿Esta seguro de eliminar este Publisher?')" class="fa-trash-o tt-lt" data-toggle="tooltip" title="" data-original-title="Eliminar Publisher"></a></li>
														<?php
														}
														?>
								
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
		jQuery.extend( jQuery.fn.dataTableExt.oSort, {
		    "date-uk-pre": function ( a ) {
		        if (a == null || a == "") {
		            return 0;
		        }
		        var ukDatea = a.split('/');
		        return (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
		    },
		 
		    "date-uk-asc": function ( a, b ) {
		        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
		    },
		 
		    "date-uk-desc": function ( a, b ) {
		        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
		    }
		} );
		
		jQuery(document).ready(function($){
            $( "#tabs" ).tabs();
			$('#tbl-estats').dataTable({
				"paging": false,
				"lengthChange": false,
				"searching": false,
				"info":     false,
				"order": [[ 0, "desc" ]],
				columnDefs: [
			       { type: 'date-uk', targets: 3 }
			     ]
    		});
		});
	</script>
	
</body>
</html>