<?php
	session_start();
	define('CONST',1);
	require('../config.php');
	require('../db.php');
	require('../constantes.php');
	require('../common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);

	if($_SESSION['Type'] == 3 || $_SESSION['Type'] == 2){
		
	}else{
		header('Location: login.php');
		exit(0);
	}

	if(isset($_GET['del'])){
		if($_GET['del'] > 0){
			$idDel = intval($_GET['del']);
			//$sql = "DELETE FROM " . USERS . " WHERE id = '$idDel' LIMIT 1";
			$sql = "UPDATE " . USERS . " SET deleted = 1 WHERE id = '$idDel' AND type = 2 LIMIT 1";
			$db->query($sql);
			header('Location: advertisers.php');
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
						<div class="titl">Control de Anunciantes</div>
					</div>
					<div class="bx-bd">
						<div class="bx-pd">
							<ul class="lst-tbs b-fx mb2">
								<!--<li class="active"><a href="#">Datos</a></li>
								<li>
									<div class="fs-dropdown slct-hd srch-inp dropdown" tabindex="0">
                                        <input type="text" class="hauto" placeholder="Busca aqui otra web, escribela" data-toggle="dropdown">
										<div class="dropdown-menu">											
											<div class="fs-dropdown-options bx-cn">
												<span class="fs-dropdown-group">Resultados de tu busqueda</span>
												<button type="button" class="fs-dropdown-item" data-value="1"><strong>Fotosd</strong>eguarras.com</button>
												<button type="button" class="fs-dropdown-item" data-value="2"><strong>Fotosd</strong>eguarras.com</button>
											</div>
										</div>
									</div>
								</li>-->
								<li class="b-rt"><a href="add-advertiser.php" class="fa-plus-circle">Añadir Anunciante</a></li>
							</ul>					
							<!--<table>-->
							<div class="tbl-cn">
								<table id="tbl-estats">
									<thead>
										<tr>
											<th>Usuario</th>
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
										$WM = "";
									}elseif($_SESSION['Type'] == 2){
										$idAccM = $_SESSION['idAdmin'];
										$WM = " AND AccM = '$idAccM'";
									}
									$sql = "SELECT * FROM " . USERS . " WHERE type = 2 AND deleted = 0 $WM ORDER BY user ASC";
									$query = $db->query($sql);
									if($db->num_rows($query) > 0){
										while($User = $db->fetch_array($query)){
											$aR = explode('-',$User['date']);
											$Fecha = $aR[2] . '/' . $aR[1] . '/' . $aR[0];
											?><tr>
												<td data-title="Usuario"><?php echo $User['user']; ?></td>
												<td data-title="ID">ID: <?php echo $User['id']; ?></td>
												<td data-title="Email"><?php echo $User['email']; ?></td>
												<td data-title="Agregado"><?php echo $Fecha; ?></td>
												<td data-title="Ingreso"><?php if($User['lastlogin'] > 0 ) { echo date('d.m.Y', $User['lastlogin']); ?> <span class="clr-gry"><?php echo date('H:i',$User['lastlogin']); ?></span><?php } else { echo 'NA'; } ?></td>
												<td data-title="Opciones">
													<ul class="lst-opt">
														<!--<li><a href="logon.php?iduser=<?php echo $User['id']; ?>" target="_blank" class="fa-home tt-lt" data-toggle="tooltip" title="" data-original-title="Entrar en Cuenta"></a></li>-->
														<li><a href="edit-advertiser.php?iduser=<?php echo $User['id']; ?>" class="fa-edit tt-lt" data-toggle="tooltip" title="" data-original-title="Editar Publisher"></a></li>
														<li><a href="index.php?del=" onclick="return confirm('¿Esta seguro de eliminar este Anunciante?')" class="fa-trash-o tt-lt" data-toggle="tooltip" title="" data-original-title="Eliminar Publisher"></a></li>
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
   
	<!--<modal [ mdl-politcs ]>-->
	<div class="modal fade" id="mdl-politcs" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content bx-cn bx-shnone-modal">			
				<div class="bx-hd dfl b-fx bghd-b">
					<span>Políticas de trabajo y conductas no permitidas</span>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="fa-times-circle"></span></button>
				</div>
				<div class="bx-bd">
					<div class="bx-pd d-fx">
						<div class="d-flx1">
							<ul class="lst-polts">
								<li>No se permite el falseamiento de registros, hechos por ti, alquien de tu equipo o un robot.</li>
								<li>No se permite el tráfico incentivado ni ofrecer premios o recompesas para conseguir mejores resultados en Vidoomy</li>
								<li>No se permite enlazar imagenes adultas o sexo explicito para utilizarlo con nuestras campañas de Link Directo.</li>
								<li>Solo podrás poner banners proporcionados por Yuhuads, no podrás hacer tus propios banners, si deseas hacerlo ponte en contacto con la adminitración.</li>
								<li>No se permite usar campañas de ocio en páginas de contenido adulto y viceversa.</li>
							</ul>
							<p><strong>El no cumplimiento de estas políticas supondrá el cierre inmediato de la cuenta y el no pago de cualquier factura pendiente o saldo generado.</strong></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
  	<!--</modal [ mdl-politcs ]>-->
    
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