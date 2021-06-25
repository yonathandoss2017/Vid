<?php
	session_start();
	define('CONST',1);
	require('../config.php');
	require('../db.php');
	require('../constantes.php');
	require('../common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);

	if(isset($_POST['user'])){
		
		if(isset($_SESSION['LoginFail'])){
			if($_SESSION['LoginFail'] >= 5){
				if(strlen($_POST['g-recaptcha-response']) >= 10){
					
				}else{
					header('Location: login.php');
					exit(0);
				}
			}
		}
		
		$PUser = my_clean(trim($_POST['user']));
		if(strlen($_POST['pass']) == 32){
			$PPass = my_clean($_POST['pass']);
		}else{
			$PPass = md5(trim($_POST['pass']));
		}
		
		$sql = "SELECT id FROM " . ACC_MANAGERS. " WHERE Email = '$PUser' AND Password = '$PPass' AND Deleted = 0 AND (Type = 3 OR id = 11) LIMIT 1 ";
		$idAdmin = $db->getOne($sql);
		
		if($idAdmin > 0){
			$_SESSION['Admin'] = 1;
			$_SESSION['idAdmin'] = $idAdmin;
			
			$sql = "SELECT Type FROM " . ACC_MANAGERS. " WHERE id = '$idAdmin' LIMIT 1";
			$Type = $db->getOne($sql);
			$_SESSION['Type'] = $Type;
			
			if(strlen($_POST['pass']) != 32){
				$Time = time();
				$IP = $_SERVER['REMOTE_ADDR'];
				$sql = "UPDATE " . ACC_MANAGERS. " SET LastLogin = '$Time', IP = '$IP' WHERE id = '$idAdmin' LIMIT 1";
				$db->query($sql);
			}
		}else{
			if(isset($_SESSION['LoginFail'])){
				$_SESSION['LoginFail']++;
			}else{
				$_SESSION['LoginFail'] = 1;
			}
		}
		/*
		if($_POST['user']=="admin" && $_POST['pass']=="Vidoomy%1"){
			$_SESSION['Admin'] = 1;
		}
		*/
	}
	if(isset($_SESSION['Admin'])){
		if($_SESSION['Admin']!=1){
			header('Location: login.php');
			exit(0);
		}
	}else{
		header('Location: login.php');
		exit(0);
	}

	if(isset($_GET['del'])){
		if($_GET['del'] > 0){
			$idDel = intval($_GET['del']);
			//$sql = "DELETE FROM " . USERS . " WHERE id = '$idDel' LIMIT 1";
			$sql = "UPDATE " . USERS . " SET deleted = 1 WHERE id = '$idDel' AND type = 0 LIMIT 1";
			$db->query($sql);
			header('Location: index.php');
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
								<button type="button" class="fs-dropdown-selected fs-touch-element" data-toggle="dropdown"><?php if(isset($_GET['a'])){ echo "Aceptados"; } else { echo "Alta Manual"; } ?></button>
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
											<th>Usuario / Nick</th>
											<th>ID</th>
											<th>E-Mail</th>
											<th>Agregado</th>
											<th>Último Ingreso</th>
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
									if($_SESSION['Type'] == 3){
										$WM = " AND  AccM != 15 AND AccM != 9999 ";
									}elseif($_SESSION['Type'] == 1){
										$idAccM = $_SESSION['idAdmin'];
										$WM = " AND AccM = '$idAccM' ";
									}
									if($_SESSION['Type'] == 3){
										if(isset($_GET['a'])){
											$AA = "AND verify_code != ''";
										}else{
											$AA = "AND verify_code = ''";
										}
									}else{
										$AA = '';
									}
									$sql = "SELECT * FROM " . USERS . " WHERE deleted = 0 AND type = 0 $AA $WM ORDER BY user ASC";
									//echo '<!--'.$sql.'-->';
									$query = $db->query($sql);
									if($db->num_rows($query) > 0){
										while($User = $db->fetch_array($query)){
											$aR = explode('-',$User['date']);
											$Fecha = $aR[2] . '/' . $aR[1] . '/' . $aR[0];
											?><tr>
												<td data-title="Usuario"><?php if($User['nick'] != '') { echo $User['nick']; } else { echo $User['user']; } ?></td>
												<td data-title="ID">ID: <?php echo $User['id']; ?></td>
												<td data-title="Email"><?php echo $User['email']; ?></td>
												<td data-title="Agregado"><?php echo $Fecha; ?></td>
												<td data-title="Ingreso"><?php if($User['lastlogin'] > 0 ) { echo date('d.m.Y', $User['lastlogin']); ?> <span class="clr-gry"><?php echo date('H:i',$User['lastlogin']); ?></span><?php } else { echo 'NA'; } ?></td>
												<td data-title="Opciones">
													<ul class="lst-opt">
														<li><a href="logon.php?iduser=<?php echo $User['id']; ?>" target="_blank" class="fa-home tt-lt" data-toggle="tooltip" title="" data-original-title="Entrar en Cuenta"></a></li>
														<li><a href="edit-user.php?iduser=<?php echo $User['id']; ?>" class="fa-edit tt-lt" data-toggle="tooltip" title="" data-original-title="Editar Publisher"></a></li>
														<li><a href="pages.php?iduser=<?php echo $User['id']; ?>" class="fa-eye tt-lt" data-toggle="tooltip" title="" data-original-title="Ver/Editar Paginas"></a></li>
														<li><a href="tags.php?iduser=<?php echo $User['id']; ?>" class="fa-list-alt tt-lt" data-toggle="tooltip" title="" data-original-title="Ver/Editar Zonas"></a></li>
														<li><a href="add-revenue.php?iduser=<?php echo $User['id']; ?>" class="fa-magic tt-lt" data-toggle="tooltip" title="" data-original-title="Agregar Revenue Manual"></a></li>
														
														
														<li><a href="index.php?del=" onclick="return confirm('¿Esta seguro de eliminar este Publisher?')" class="fa-trash-o tt-lt" data-toggle="tooltip" title="" data-original-title="Eliminar Publisher"></a></li>
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
				
				columnDefs: [
			       { type: 'date-uk', targets: 3 }
			     ]
    		});
		});
	</script>
	
</body>
</html>