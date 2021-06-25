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
	
	if($_SESSION['Admin']!=1){
		header('Location: login.php');
		exit(0);
	}elseif($_SESSION['Type'] != 3){
		header('Location: index.php');
		exit(0);	
	}
	
	$jsonData = file_get_contents('http://pixel.vidoomy.com/viewsresults.php');
	if($jsonData != 'null'){
		$CodeDetect = true;
		$siteData = json_decode($jsonData, true);
	}else{
		echo 'Data not found';
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
    <link rel="stylesheet" href="css/fa.css">
    <link rel="stylesheet" href="css/css.css">
    <link rel="icon" type="image/png" href="img/favicon.png">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
</head>
<body>

	<!--<all>-->
	<div class="all">
	
		<?php include 'header.php'; ?>
		
		<!--<bdcn>-->
		<div class="bdcn">
			<div class="cnt">
			<!--<Control de Estadísticas>-->
				<div class="bx-cn bx-shnone">
					<div class="bx-hd dfl b-fx bghd-b">
						<div class="titl">Control de Estadísticas</div>
						<div class="d-rt a-o2" style="margin: 3px 3px 0 auto;">
							<div class="fs-dropdown slct-hd dropdown" tabindex="0">
								<button type="button" class="fs-dropdown-selected fs-touch-element" data-toggle="dropdown"><?php
						
									if(isset($_GET['range'])){
										$range = $_GET['range'];
									}else{
										$range = '';
									}
									
									$dfrom = '';
									$dto = '';
									if($range == 'today'){
										echo 'Estádisticas de Hoy';
									}elseif($range == 'yesterday'){
										echo 'Estádisticas de Ayer';
									}elseif($range == 'last'){
										echo '&Uacute;ltimos 7 D&iacute;as';
									}elseif($range == 'custom'){
										$customedates = true;
										if(isset($_POST['dfrom']) && isset($_POST['dto'])){
											$dfrom = $_POST['dfrom'];
											$dto = $_POST['dto'];
										}elseif(isset($_GET['dfrom']) && isset($_GET['dto'])){
											$dfrom = $_GET['dfrom'];
											$dto = $_GET['dto'];
										}else{
											$customedates = false;
											echo 'Estadísticas mes Actual';
										}
										if($customedates){
											echo $dfrom . ' - ' . $dto;
											$andr = 'range=custom&dfrom='.$dfrom.'&dto='.$dto.'&';
										}
									}else{
										echo 'Estadísticas de Hoy';
									}
								?></button>
								<div class="dropdown-menu">											
									<div class="fs-dropdown-options bx-cn">
										<span class="fs-dropdown-group">Seleccionar Fecha</span>
											<button type="button" class="fs-dropdown-item" data-value="1" onclick="location.href='statsviews.php?range=today'">Estádisticas de Hoy</button>
											<button type="button" class="fs-dropdown-item" data-value="2" onclick="location.href='statsviews.php?range=yesterday'">Estádisticas de Ayer</button>
											<button type="button" class="fs-dropdown-item" data-value="3" onclick="location.href='statsviews.php?range=last'">&Uacute;ltimos 7 D&iacute;as</button>
											<div class="frm-fltr">
												<form action="statsviews.php?range=custom" method="post">
													<p>Personaliza las Fechas</p>
													<div class="clsb-fx">
														<div class="clmd06">
															<div class="frm-group">
																<label>Desde</label>
																<div class="d-flx1">
																	<label>
																		<input type="text" name="dfrom" placeholder="<?php echo date('d.m.Y'); ?>" value="<?php echo $dfrom; ?>" id="datepicker" />
																	</label>
																</div>
															</div>
														</div>
														<div class="clmd06">
															<div class="frm-group">
																<label>Hasta</label>
																<div class="d-flx1">
																	<label>
																		<input type="text" name="dto" placeholder="<?php echo date('d.m.Y'); ?>" value="<?php echo $dto; ?>" id="datepicker2" />
																	</label>
																</div>
															</div>
														</div>
													</div>
													<div>
														<button class="btn fa-calendar-o">Aplicar Filtro</button>
													</div>
												</form>
											</div>
										</div>
									</div>
								</div>
							</div>
						
					</div>
					<div class="bx-bd">
						<div class="bx-pd">
							<?php
								if($range == 'today'){
									$FirstDay = date('Y-m-d');
									$LastDay = date('Y-m-d');
								}elseif($range == 'yesterday'){
									$FirstDay = date('Y-m-d',time() - 86400);
									$LastDay = date('Y-m-d',time() - 86400);
								}elseif($range == 'last'){
									$FirstDay = date('Y-m-d',strtotime("-6 days"));
									$LastDay = date('Y-m-d');
								}elseif($range == 'custom'){
									$arDfrom = explode('.',$dfrom);
									$FirstDay = $arDfrom[2] . '-' . $arDfrom[1] . '-' . $arDfrom[0];
									$arDto = explode('.',$dto);
									$LastDay = $arDto[2] . '-' . $arDto[1] . '-' . $arDto[0];
								}else{
									$FirstDay = date('Y-m-d');
									$LastDay = date('Y-m-d');
								}
								
								$Time1 = strtotime($LastDay);
								$Time2 = strtotime($FirstDay);
								$Diff = $Time1 - $Time2;
								
								$Days = round($Diff / 86400) + 1;
								
								if($Days <= 7){
							?>
							<!--<table>-->
							<div class="tbl-cn">
								<table id="tbl-estats">
									<thead>
										<tr>
											<th>Publisher</th><?php
												

												for($d = $Days; $d >= 1; $d--){
													$Date = date('d-m-Y',$Time1);
													?><th><?php echo $Date;?></th><?php
													$Time1 = $Time1 - 86400;
												}
										?></tr>
									</thead>
									
									<tbody>
										<?php
											$sql = "SELECT * FROM " . USERS . " WHERE deleted = 0 AND AccM = 15 ORDER BY user ASC";
											$query = $db->query($sql);
											if($db->num_rows($query) > 0){
												while($User = $db->fetch_array($query)){
													?><tr>
													<td><a href="aprove.php?iduser=<?php echo $User['id']; ?>"><?php echo $User['user']; ?></a></td><?php
														
														$Time1 = strtotime($LastDay);
														//unset($Sites);
														$Sites = array();
														$sql = "SELECT id FROM " . SITES . " WHERE idUser = '" . $User['id'] . "'";
														
														$query2 = $db->query($sql);
														if($db->num_rows($query2) > 0){
															while($Site = $db->fetch_array($query2)){
																$Sites[$Site['id']] = $Site['id'];
															}
														}
														
														for($d = $Days; $d >= 1; $d--){
															$Date = date('Y-m-d',$Time1);
															$SumViews = 0;
															if(count($Sites) > 0){
																foreach($Sites as $idSite){
																	if(isset($siteData[$idSite][$Date])){
																		$SumViews += $siteData[$idSite][$Date];
																	}
																}
															}
															?><th style="text-align:center;"><?php echo $SumViews; ?></th><?php
															$Time1 = $Time1 - 86400;
														}
													?></tr><?php
												}
											}
										?>
									</tbody>
								</table>
							</div>
							<!--</table>--><?php
							}else{
								
								?><div style="text-align:center;">El rango de fechas no puede ser superior a 7 d&iacute;as.</div><?php
								
							}
							
							?>
						</div>
						
					</div>
				</div>
				<!--</Control de Estadísticas>-->
										
				
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
	<script src="js/lib/jquery-ui.js"></script>
	
	<script>
	  $( function() {
		$( "#datepicker" ).datepicker({ dateFormat: 'dd.mm.yy' });
		$( "#datepicker2" ).datepicker({ dateFormat: 'dd.mm.yy' });
	  } );
	</script>
	<!-- Tables -->
    <script src="js/jquery.dataTables.min.js"></script>
    <script>
		jQuery.fn.dataTableExt.oSort['numeric-comma-asc']  = function(a,b) {
			var x = (a == "-") ? 0 : a.replace( /$/, "" );
			var y = (b == "-") ? 0 : b.replace( /$/, "" );
			x = parseFloat( x );
			y = parseFloat( y );
			return ((x < y) ? -1 : ((x > y) ?  1 : 0));
		};

		jQuery.fn.dataTableExt.oSort['numeric-comma-desc'] = function(a,b) {
			var x = (a == "-") ? 0 : a.replace( /$/, "" );
			var y = (b == "-") ? 0 : b.replace( /$/, "" );
			x = parseFloat( x );
			y = parseFloat( y );
			return ((x < y) ?  1 : ((x > y) ? -1 : 0));
		};
		jQuery(document).ready(function($){
			$('#tbl-estats').dataTable({
				"paging": false,
				"searching": false,
				"order": [[ 1, "desc" ]],
				"info": false
    		});
		});
    </script>
	
</body>
</html>