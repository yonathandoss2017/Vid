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
	require('libs/display.lib.php');
	
	$dbuser2 = "root";
	$dbpass2 = "ViDo0-PROD_2020";
	$dbhost2 = "aa12gqfb9qs8z09.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	require '/var/www/html/site/include/PHPMailer/PHPMailerAutoload.php';
	$mail = new PHPMailer;
		
	if(isset($_GET['r']) && isset($_GET['m'])){
		if($_GET['r'] > 0){
			$idR = intval($_GET['r']);
			$sql = "UPDATE " . SITES . " SET eric = 3 WHERE id = '$idR' LIMIT 1";
			$db->query($sql);
			
			$Message = urldecode($_GET['m']);
			
			$sql = "INSERT INTO rejected_sites (idSite, Reason) VALUES ($idR, '$Message')";
			$db->query($sql);
			
			$sql = "UPDATE website SET status = 4, rejection_reason = '$Message' WHERE id = '$idA' LIMIT 1";
			$db2->query($sql);
			
			newGenerateJS($idR);
			notifySiteEric($idR, false, $Message);
			
			header('Location: aproval.php');
			exit(0);
		}
	}
	
	if(isset($_GET['a'])){
		if($_GET['a'] > 0){
			$idA = intval($_GET['a']);
			$sql = "UPDATE " . SITES . " SET eric = 2 WHERE id = '$idA' LIMIT 1";
			$db->query($sql);
			
			$sql = "UPDATE website SET status = 1 WHERE id = '$idA' LIMIT 1";
			$db2->query($sql);
			
			newGenerateJS($idA);
			notifySiteEric($idA, true);
			
			header('Location: aproval.php');
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
						<div class="titl">Aprobación de Sitios</div>
						<div class="d-rt a-o2" style="margin: 3px 3px 0 auto;">
							<div class="fs-dropdown slct-hd dropdown" tabindex="0">
								<button type="button" class="fs-dropdown-selected fs-touch-element" data-toggle="dropdown">
								<?php if(isset($_GET['aprov'])){ echo "Aprobados"; $erics = 2; } elseif(isset($_GET['rej'])) { echo "Rechazados"; $erics = 3; } else { echo "Pendientes de Aprobación"; $erics = 1; } ?></button>
								<div class="dropdown-menu">							
									<div class="fs-dropdown-options bx-cn">
										<span class="fs-dropdown-group">Seleccionar</span>
										<button type="button" class="fs-dropdown-item" onclick="location.href='aproval.php'">Pendientes de Aprobación (<?php
										$sql = "SELECT COUNT(id) FROM " . SITES . " WHERE eric = 1";
										echo $db->getOne($sql);
										?>)</button>
										<button type="button" class="fs-dropdown-item" onclick="location.href='aproval.php?aprov=1'">Aprobados (<?php
										$sql = "SELECT COUNT(id) FROM " . SITES . " WHERE eric = 2";
										echo $db->getOne($sql);
										?>)</button>
										<button type="button" class="fs-dropdown-item" onclick="location.href='aproval.php?rej=1'">Rechazados (<?php
										$sql = "SELECT COUNT(id) FROM " . SITES . " WHERE eric = 3";
										echo $db->getOne($sql);
										?>)</button>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="bx-bd">
						<div class="bx-pd">
							<div class="cls c-fx">
								<div class="clmd04">
									<div class="frm-group d-fx lbl-lf">
										<label for="searchPage">Buscar:</label>
										<input type="text" id="searchPage" class="d-flx1">
									</div>
								</div>
							</div>
							
<script>
function reject(idsite) {
  var txt;
  var answ = prompt('¿Esta seguro de RECHAZAR este sitio? Si es así, indica los motivos para el rachazo');
  if (answ == null || answ == "") {
    return false;
  } else {
    window.location = 'aproval.php?r=' + idsite + '&m=' + encodeURI(answ);
    return false;
  }
}
</script>

							<!--<table>-->
							<div class="tbl-cn">
								<table id="tbl-estats">
									<thead>
										<tr>
											<th>Sitio</th>
											<th>Fecha de Creación</th>
											<th>Publisher</th>
											<th>Publisher Manager</th>
											<th class="no-sort">Opciones</th>
										</tr>
									</thead>
									<tbody><?php
									$sql = "SELECT s.id, s.sitename, s.siteurl, s.time, u.user, a.Name FROM " . SITES . " s
									INNER JOIN " . USERS . " u ON u.id = s.idUser 
									INNER JOIN " . ACC_MANAGERS . " a ON a.id = u.AccM 
									WHERE s.deleted != 1 AND s.eric = '$erics' ORDER BY s.sitename";
									
									$query = $db->query($sql);
									if($db->num_rows($query) > 0){
										while($Site = $db->fetch_array($query)){
											?><tr>
												<td data-title="URL"><?php echo $Site['siteurl']; ?></td>
												<td data-title="DateTime"><?php echo date('d-m-Y H:i:s', $Site['time']); ?></td>
												<td data-title="Publisher"><?php echo $Site['user']; ?></td>
												<td data-title="PublisherM"><?php echo $Site['Name']; ?></td>
												<td data-title="Opciones">
													<ul class="lst-opt">
														<li><a href="edit-page.php?idpage=<?php echo $Site['id']; ?>" class="fa-edit tt-lt" data-toggle="tooltip" title="" data-original-title="Editar"></a></li><?php
															if($erics == 1){
														?>
														<li><a href="aproval.php?a=<?php echo $Site['id']; ?>" class="fa-thumbs-up tt-lt" data-toggle="tooltip" title="" data-original-title="Aprobar"></a></li>
														<li><a href="#" onclick="return reject(<?php echo $Site['id']; ?>)" class="fa-thumbs-down tt-lt" data-toggle="tooltip" title="" data-original-title="Rechazar"></a></li><?php
															}
															if($erics == 3){
																$sql = "SELECT Reason FROM rejected_sites WHERE idSite = '" . $Site['id'] . "' LIMIT 1";
																$Reason = $db->getOne($sql);
														?>
														<li><a href="#" class="fa-info-circle tt-lt" data-toggle="tooltip" title="" data-original-title="<?php echo $Reason; ?>"></a></li>
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
		jQuery(document).ready(function($){
            $( "#tabs" ).tabs();
			var table = $('#tbl-estats').DataTable({
				"columnDefs": [{ targets: 'no-sort', orderable: false }],
				"paging": true,
				"info": false,
				"pageLength": 100,
				"lengthChange": false,
				"searching": true,
				"dom": 'lrtip',
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
				}
    		});
    		

			
			$('#searchPage').on( 'keyup', function () {
				table.column(0).search(this.value).draw();
			});

		});
	</script>
	
</body>
</html>