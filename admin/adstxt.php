<?php
	session_start();
	define('CONST',1);
	if($_SESSION['Type'] != 3){
		header('Location: login.php');
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
			$sql = "DELETE FROM " . ADSTXT . " WHERE id = '$idDel' AND NoEdit = 0 LIMIT 1";
			$db->query($sql);
			
			header('Location: adstxt.php');
			exit(0);
		}
	}
	
	$lineError = '';
	$lineErrors = '';
	$lineErrorc = '';
	
	$line = '';
	
	if(isset($_POST['save'])){					
		if($_POST['save'] != ''){
			if(!empty($_POST['line'])){
				
				$line = my_clean($_POST['line']);
				$Time = time();
				
				$sql = "INSERT INTO " . ADSTXT . " (LineTxt, Time) VALUES ('$line', '$Time')";
				$db->query($sql);
										
				header('Location: adstxt.php');
				exit(0);
				
			}
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
						<div class="titl">Control Ads.txt</div>
					</div>
					<div class="bx-bd">
						<div class="bx-pd">
							<ul class="lst-tbs b-fx mb2">
								<li class="b-rt"><a href="#" id="addline" class="fa-plus-circle">Añadir Línea</a></li>
							</ul>					
							<div class="bx-bd" id="addlineform">
								<div class="bx-pd">
									<form action="" method="post" class="frm-adrsit">
										<div class="bx-hd dfl b-fx">
											<div class="titl">Añadir nueva Línea</div>
										</div>
										<div class="clsd-fx">
											<div class="clmd12">
												<!--<Línea>-->
												<div class="frm-group d-fx lbl-lf<?php echo $lineErrorc; ?>">
													<label<?php echo $lineErrors; ?>>Línea</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $lineError; ?>>
															<input type="text" name="line" value="<?php echo $line; ?>" />
														</label>
													</div>
												</div>
												<!--</Línea>-->
											</div>
										</div>
										<div class="botnr-cn">
											<input type="submit" class="fa-save" value="Agregar" name="save" />
										</div>
									</form>
								</div>
							</div>
							
							<div class="tbl-cn">
								<table id="tbl-estats">
									<thead>
										<tr>
											<th>Nro</th>
											<th>Línea</th>
											<th>Opciones</th>
										</tr>
									</thead>
									<tbody><?php
									$sql = "SELECT * FROM " . ADSTXT . " ORDER BY id DESC";
									$query = $db->query($sql);
									if($db->num_rows($query) > 0){
										while($Line = $db->fetch_array($query)){
											?><tr>
												<td data-title="Nro"><?php echo $Line['id']; ?></td></td>
												<td data-title="Linea"><?php echo $Line['LineTxt']; ?></td>
												<td data-title="Opciones">
													<ul class="lst-opt"><?php
														if($Line['NoEdit'] != 1){
														?><!--<li><a href="edit-line.php?id=<?php echo $Line['id']; ?>" class="fa-edit tt-lt" data-toggle="tooltip" title="" data-original-title="Editar Línea"></a></li>-->
														<li><a href="adstxt.php?del=<?php echo $Line['id']; ?>" onclick="return confirm('¿Esta seguro de eliminar esta línea?')" class="fa-trash-o tt-lt" data-toggle="tooltip" title="" data-original-title="Eliminar Línea"></a></li><?php
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
			$('#tbl-estats').dataTable({
				"paging": false,
				"lengthChange": false,
				"searching": false,
				"info":     false
    		});
    		
    		$('#addlineform').hide();
    		$('#addline').click(function(e){
	    		e.preventDefault();
	    		$('#addlineform').show();
    		});
		});
	</script>
	
</body>
</html>