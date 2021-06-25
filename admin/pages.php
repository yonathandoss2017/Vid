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
	
	$idUser = 0;
	$linkAnd = '';
	$link = '';
	if(isset($_GET['iduser'])){
		$idUser = intval($_GET['iduser']);
		if($idUser > 0){
			$sql = "SELECT * FROM " . USERS . " WHERE id = '$idUser' LIMIT 1";
			$query = $db->query($sql);
			$userData = $db->fetch_array($query);
			
			$linkAnd = "&iduser=$idUser";
			$link = "?iduser=$idUser";
		}
	}
	
	if($_SESSION['Type'] != 3 && $idUser > 0){
		$sql = "SELECT AccM FROM " . USERS . " WHERE id = '$idUser' LIMIT 1";
		if($db->getOne($sql) != $_SESSION['idAdmin']){
			header('Location: index.php');
			exit(0);
		}
	}
	
	if(isset($_GET['del'])){
		if($_SESSION['Type'] == 3) {
			if($_GET['del'] > 0){
				$idDel = intval($_GET['del']);
				$sql = "UPDATE " . SITES . " SET deleted = 1 WHERE id = '$idDel' LIMIT 1";
				$db->query($sql);
				header('Location: pages.php');
				exit(0);
			}
		}
	}
	
	if(isset($_GET['blockp'])){
		if($_SESSION['Type'] == 3) {
			if($_GET['blockp'] > 0){
				$idBlock = intval($_GET['blockp']);
				$sql = "UPDATE " . SITES . " SET premium_block = 1 WHERE id = '$idBlock' LIMIT 1";
				$db->query($sql);
				header('Location: pages.php');
				exit(0);
			}
		}
	}
	
	if(isset($_GET['noblockp'])){
		if($_SESSION['Type'] == 3) {
			if($_GET['noblockp'] > 0){
				$idBlock = intval($_GET['noblockp']);
				$sql = "UPDATE " . SITES . " SET premium_block = 0 WHERE id = '$idBlock' LIMIT 1";
				$db->query($sql);
				header('Location: pages.php');
				exit(0);
			}
		}
	}
	
	$categories[1] = 'Periódico';
	$categories[2] = 'TV online';
	$categories[3] = 'Viral';
	$categories[4] = 'Juegos';
	$categories[10] = 'Otros';	
	
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
						<div class="titl">Control de Paginas<?php if($idUser > 0) { echo ' - Usuario: ' . $userData['user']; } ?></div>
					</div>
					<div class="bx-bd">
						<div class="bx-pd">
							<div class="cls c-fx">
								<div class="clmd04">
									<div class="frm-group d-fx lbl-lf">
										<label for="searchPage">Pagina:</label>
										<input type="text" id="searchPage" class="d-flx1">
									</div>
								</div>
								<div class="clmd04">
									<div class="frm-group d-fx lbl-lf">
										<label for="adstxtstatus">Ads.txt:</label>
										<div class="d-flx1">
											<select id="adstxtstatus">
												<option value="">Todas</option>
												<option value="correct">Correcto</option>
												<option value="inco">Con Fallos</option>
											</select>
										</div>
									</div>
								</div>

								<div class="clmd04">
									<div class="frm-group d-fx lbl-lf">
										<ul class="lst-tbs b-fx mb2" style="margin: auto; padding-top: 5px;"><?php
											if($_SESSION['Type'] == 3){
											?><li class="b-rt"><a href="adstxt.php" class="fa-plus-circle">Editar Ads.txt</a></li><?php } ?>
											<li class="b-rt"><a href="add-site.php<?php echo $link; ?>" class="fa-plus-circle">Añadir Sitio</a></li>
										</ul>
									</div>

								</div>
							</div>
							

							<!--<table>-->
							<div class="tbl-cn">
								<table id="tbl-estats">
									<thead>
										<tr>
											<th>Nombre</th>
											<th>URL</th>
											<!--<th>Categoria</th>-->
											<th>Usuario</th>
											<th>Ads.txt</th>
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
									$sql = "SELECT COUNT(*) FROM " . ADSTXT . "";
									$tlines = $db->getOne($sql);
									
									$where = '';
									if($idUser > 0){
										$sql = "SELECT * FROM " . SITES . " WHERE deleted != 1 AND idUser = '$idUser' AND aproved = 0 ORDER BY sitename ASC";
									}else{
										if($_SESSION['Type'] != 3){
											$idAccM = $_SESSION['idAdmin'];
											 $sql = "SELECT sites.id AS id, sites.sitename AS sitename, sites.siteurl AS siteurl, sites.idUser AS idUser, sites.category AS category, sites.adstxt AS adstxt FROM sites INNER JOIN users ON users.id = sites.idUser WHERE users.AccM = '$idAccM' AND sites.deleted = 0 AND sites.aproved = 0 ORDER BY sites.sitename ASC";
										}else{
											$sql = "SELECT * FROM " . SITES . " WHERE deleted = 0 AND aproved = 0 ORDER BY sitename ASC";
										}
									}
									$query = $db->query($sql);
									if($db->num_rows($query) > 0){
										while($Site = $db->fetch_array($query)){
											if($Site['adstxt'] == 1){
												$ml = explode(',',$Site['mlines']);
												$hl = $tlines - count($ml);
												$Cnt = $hl . '/' . $tlines; 
											}else{
												$Cnt = '';
											}
											$sql = "SELECT user FROM " . USERS . " WHERE id = '" . $Site['idUser'] . "' LIMIT 1";
											$User = $db->getOne($sql);
											?><tr>
												<td data-title="Nombre"><?php echo $Site['sitename']; ?></td>
												<td data-title="URL"><?php echo $Site['siteurl']; ?></td>
												<td data-title="Usuario"><?php echo $User; ?></td>
												<td data-title="Adstxt" style="text-align:center;"><a href="checkads.php?idsite=<?php echo $Site['id']; ?>"><?php
													if($Site['adstxt'] == 0){
														?><span class="fa-check-circle"><span style="display:none;">correct</span></span><?php
														
													}else{
														?><span class="fa-times-circle"><span style="display:none;">inco</span></span> <?php
													}
													echo $Cnt;
													?></a>
												</td>
												<td data-title="Opciones">
													<ul class="lst-opt">
														<li><a href="edit-page.php?idpage=<?php echo $Site['id']; ?>" class="fa-edit tt-lt" data-toggle="tooltip" title="" data-original-title="Editar Pagina"></a></li>
														<li><a href="tags.php?iduser=<?php echo $Site['idUser']; ?>&idsite=<?php echo $Site['id']; ?>" class="fa-list-alt tt-lt" data-toggle="tooltip" title="" data-original-title="Ver/Editar Zonas"></a></li>
														<?php
														if($_SESSION['Type'] == 3){
														?>
														<li><a href="pages.php?del=<?php echo $Site['id']; ?><?php echo $linkAnd; ?>" onclick="return confirm('¿Esta seguro de eliminar esta pagina? La misma no podrá ser recuperada')" class="fa-trash-o tt-lt" data-toggle="tooltip" title="" data-original-title="Eliminar Pagina"></a></li><?php
														if($Site['premium_block'] == 1){
															?><li><a href="pages.php?noblockp=<?php echo $Site['id']; ?><?php echo $linkAnd; ?>" onclick="return confirm('¿Esta seguro de quitar el bloqueo?')" class="fa-check tt-lt" data-toggle="tooltip" title="" data-original-title="Quitar Bloqueo Premium"></a></li>
														<?php
														}else{
															?><li><a href="pages.php?blockp=<?php echo $Site['id']; ?><?php echo $linkAnd; ?>" onclick="return confirm('¿Esta seguro de quitar este dominio del pago premium?')" class="fa-ban tt-lt" data-toggle="tooltip" title="" data-original-title="Bloquear Premium"></a></li>
														<?php
															}
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
		jQuery(document).ready(function($){
            $( "#tabs" ).tabs();
			var table = $('#tbl-estats').DataTable({
				"columnDefs": [{ targets: 'no-sort', orderable: false }],
				"paging": true,
				"pageLength": 100,
				"lengthChange": false,
				"searching": true,
				"dom": 'lrtip',
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
				}
    		});
    		
    		$('#adstxtstatus').change(function(){
				table.column(3).search(this.value).draw();
			});
			
			$('#searchPage').on( 'keyup', function () {
				table.column(1).search(this.value).draw();
			});

		});
	</script>
	
</body>
</html>