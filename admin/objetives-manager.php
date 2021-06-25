<?php
	session_start();
	define('CONST',1);
	if($_SESSION['Type'] == 3){
		
	}else{
		header('Location: login.php');
		exit(0);
	}
	require('../config.php');
	require('../db.php');
	require('../constantes.php');
	require('../common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	if(isset($_GET['idam'])){
		$idAccM = intval($_GET['idam']);
	}else{
		header('Location: acc-managers.php');
		exit(0);
	}
	
	$sql = "SELECT * FROM " . ACC_MANAGERS . " WHERE id = '$idAccM' LIMIT 1";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		$AccM = $db->fetch_array($query);
	}else{
		header('Location: acc-managers.php');
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
    <link rel="stylesheet" href="css/thimify.css">
    <link rel="icon" type="image/png" href="img/favicon.png">
</head>
<body>

	<!--<all>-->
	<div class="all">
	
		<?php include 'header.php'; ?>
		
		<!--<bdcn>-->
		<div class="bdcn">
			<div class="cnt">
				
				<!--<Control de objetivos>-->
				<div class="bx-cn bx-shnone">
					<div class="bx-hd dfl b-fx bghd-e">
						<div class="titl">Objetivos de <?php echo $AccM['Name']; ?></div>
					</div>
					<div class="bx-bd">
						<div class="bx-pd">
							<div class="cls c-fx">
								<div class="clmd04">
									<div class="frm-group d-fx lbl-lf">
										<ul class="lst-tbs b-fx mb2">
											<li class="b-rt"><a href="add-objetive.php?idam=<?php echo $idAccM; ?>" class="fa-plus-circle">Añadir Objetivo</a></li>
										</ul>
									</div>

								</div>
							</div>
							
							
							<div class="tbl-cn">
								<table id="tbl-estats">
									<thead>
										<tr>
											<th>Mes</th>
											<th>Publishers</th>
											<th>Pub. Premium</th>
											<th>Dominios</th>
											<th>Dom. Premium</th>
											<th>Revenue</th>
											<th>Opciones</th>
										</tr>
									</thead>
									<tbody><?php
									$sql = "SELECT * FROM " . OBJETIVES . " WHERE AccM = '$idAccM' ORDER BY id DESC";
									$query = $db->query($sql);
									if($db->num_rows($query) > 0){
										while($Obj = $db->fetch_array($query)){
											?><tr>
												<td data-title="Mes"><?php 
													$arM = explode('-',$Obj['Mes']);
													echo $MonthSpanish[$arM[1]] . ' ' . $arM[0];
												?></td></td>
												<td data-title="Publishers"><?php echo $Obj['Publishers']; ?></td>
												<td data-title="Publishers Premium"><?php echo $Obj['PublishersP']; ?></td>
												<td data-title="Dominios"><?php echo $Obj['Dominios']; ?></td>
												<td data-title="Dominios Premium"><?php echo $Obj['DominiosP']; ?></td>
												<td data-title="Revenue">$<?php echo $Obj['Revenue']; ?></td>
												<td data-title="Opciones">
													<ul class="lst-opt">
														<li><a href="edit-objetive.php?idam=<?php echo $idAccM; ?>&ido=<?php echo $Obj['id']; ?>" class="fa-edit tt-lt" data-toggle="tooltip" title="" data-original-title="Editar Objetivo"></a></li>
														<li><a href="objetives-manager.php?idam=<?php echo $idAccM; ?>&idd=<?php echo $Obj['id']; ?>" onclick="return confirm('¿Esta seguro de eliminar este objetivo?')" class="fa-trash-o tt-lt" data-toggle="tooltip" title="" data-original-title="Eliminar Objetivo"></a></li>
													</ul>
												</td>
											</tr><?php
										}
									}else{
										?><tr><td colspan="7">No hay Objetivos creados para este Publisher Manager.</td></tr><?php
									}
									?>
									</tbody>
								</table>
							</div>							
							

						</div>
						
					</div>
				</div>
				<!--</Control de objetivos>-->						
				
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
    
</body>
</html>