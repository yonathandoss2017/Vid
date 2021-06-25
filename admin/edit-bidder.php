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
		
	if(@$_SESSION['Type'] == 3){
		$idB = intval($_GET['idb']);
		if($idB > 0){
		
			$sql = "SELECT * FROM " . BIDDERS . " WHERE id = '$idB' LIMIT 1";
			$query = $db->query($sql);
			if($db->num_rows($query) > 0){
				$BidderData = $db->fetch_array($query);
				
				if(isset($_GET['d'])){
					$idDel = intval($_GET['d']);
					$sql = "UPDATE " . PLACEMENTS . " SET Deleted = 1 WHERE id = '$idDel' AND idBidder = '$idB' LIMIT 1";
					$db->query($sql);
				}
				
				$Name = $BidderData['Name'];
				$Code = $BidderData['Code'];
				$PlacementParam = $BidderData['PlacementParam'];
				$FloorParam = $BidderData['FloorParam'];
				$idParam = $BidderData['idBidderParam'];
				$idBidder = $BidderData['idBidder'];
					
				$nameError = '';
				$nameErrorc = '';
				$nameErrors = '';
				$codeError = '';
				$codeErrorc = '';
				$codeErrors = '';
				$placementError = '';
				$placementErrorc = '';
				$placementErrors = '';
					
				if(isset($_POST['save'])){
					$Name = my_clean($_POST['name']);
					$Code = my_clean($_POST['code']);
					$PlacementParam = my_clean($_POST['placement']);
					$FloorParam = my_clean($_POST['floor']);
					$idParam = my_clean($_POST['idparam']);
					$idBidder = my_clean($_POST['idbidder']);
					
					$Sigue = true;
		
					if($Name == ''){
						$Sigue = false;
						$nameError = ' data-error="Debe completar el Nombre del nuevo Bidder."';
						$nameErrorc = ' frm-rrr';
						$nameErrors = ' style="margin-bottom:20px;"';
					}
					
					if($Code == ''){
						$Sigue = false;
						$codeError = ' data-error="Debe ingresar el Código de Bidder (Ej: criteo)."';
						$codeErrorc = ' frm-rrr';
						$codeErrors = ' style="margin-bottom:20px;"';
					}
					
					if($PlacementParam == ''){
						$Sigue = false;
						$placementError = ' data-error="Debe ingresar el Placement Param (Ej: zoneID)."';
						$placementErrorc = ' frm-rrr';
						$placementErrors = ' style="margin-bottom:20px;"';
					}
					
					if($Sigue){
						$sql = "UPDATE " . BIDDERS . " SET Name = '$Name', Code = '$Code', PlacementParam = '$PlacementParam', FloorParam = '$FloorParam', idBidderParam = '$idParam', idBidder = '$idBidder' WHERE id = '$idB' LIMIT 1";
						$db->query($sql);
						
						header('Location: edit-bidder.php?idb=' . $idB);
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
								<div class="titl">Editar Bidder</div>
							</div>
							<div class="bx-bd">
								<div class="bx-pd">
									<div id="formadd">
									<form action="" method="post" class="frm-adrsit">
										<div class="bx-hd dfl b-fx">
											<div class="titl">Editar Bidder</div>
										</div>
										<div class="clsd-fx">
											<div class="clmd06">
												<!--<Nombre>-->
												<div class="frm-group d-fx lbl-lf<?php echo $nameErrorc; ?>">
													<label<?php echo $nameErrors; ?>>Nombre</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $nameError; ?>>
															<input type="text" name="name" value="<?php echo $Name; ?>" />
														</label>
													</div>
												</div>
												<!--</Nombre>-->
											</div>
											<div class="clmd06">
												<!--<Codigo>-->
												<div class="frm-group d-fx lbl-lf<?php echo $codeErrorc; ?>">
													<label<?php echo $codeErrors; ?>>Código</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $codeError; ?>>
															<input type="text" name="code" value="<?php echo $Code; ?>" />
														</label>
													</div>
												</div>
												<!--</Codigo>-->
											</div>
											<div class="clmd06">
												<!--<Placement Param>-->
												<div class="frm-group d-fx lbl-lf<?php echo $placementErrorc; ?>">
													<label<?php echo $placementErrors; ?>>Placement Param</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $placementError; ?>>
															<input type="text" name="placement" value="<?php echo $PlacementParam; ?>" />
														</label>
													</div>
												</div>
												<!--</Placement Param>-->
											</div>
											<div class="clmd06">
												<!--<Floor Param>-->
												<div class="frm-group d-fx lbl-lf">
													<label>Floor Param</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf">
															<input type="text" name="floor" value="<?php echo $FloorParam; ?>" />
														</label>
													</div>
												</div>
												<!--</Floor Param>-->
											</div>
											<div class="clmd06">
												<!--<ID Param>-->
												<div class="frm-group d-fx lbl-lf">
													<label>ID Param</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf">
															<input type="text" name="idparam" value="<?php echo $idParam; ?>" />
														</label>
													</div>
												</div>
												<!--</ID Param>-->
											</div>
											<div class="clmd06">
												<!--<ID>-->
												<div class="frm-group d-fx lbl-lf">
													<label>ID Bidder</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf">
															<input type="text" name="idbidder" value="<?php echo $idBidder; ?>" />
														</label>
													</div>
												</div>
												<!--</ID>-->
											</div>
										</div>
										<div class="botnr-cn">
											<input type="submit" class="fa-save" value="Guardar Cambios" name="save" /> 	
										</div>
									</form>
									</div>
								</div>
					
				
								<!--<Control de zonas>-->
								<div class="bx-cn bx-shnone">
									<div class="bx-hd dfl b-fx bghd-e">
										<div class="titl">Placements</div>
									</div>
									<div class="bx-bd">
										<div class="bx-pd">
											<div class="cls c-fx">
												<div class="clmd06">
													<div class="frm-group d-fx lbl-lf">
														<label for="searchPage">Nombre:</label>
														<input type="text" id="searchPlacement" class="d-flx1">
													</div>
												</div>
												<div class="clmd06">
													<div class="frm-group d-fx lbl-lf">
														<label for="active">Dispositivo:</label>
														<div class="d-flx1">
															<select id="display">
																<option value="">Todos</option>
																<option value="Desktop">Desktop</option>
																<option value="MW">MW</option>
															</select>
														</div>
													</div>
												</div>
											</div>
				
											<!--<table>-->
											<div class="tbl-cn">
												<table id="tbl-estats">
													<thead>
														<tr>
															<th>Nombre</th>
															<th>Dispositivo</th>
															<th>Tamaño</th>
															<th>Default</th>
															<th>Opciones</th>
														</tr>
													</thead>
													<tbody><?php
													$sql = "SELECT * FROM " . PLACEMENTS . " WHERE idBidder = '$idB' AND Deleted = 0 ORDER BY Name ASC";
													$query = $db->query($sql);
													if($db->num_rows($query) > 0){
														while($Place = $db->fetch_array($query)){
															if($Place['isDefault'] == 1){
																$Default = 'Si'; 
															}else{
																$Default = 'No'; 
															}
															if($Place['Platform'] == 1){
																$Platform = 'Desktop';
															}else{
																$Platform = 'MW';
															}
															$sql = "SELECT Size FROM " . DSIZES . " WHERE id = '" . $Place['Size'] . "' LIMIT 1";
															$Size = $db->getOne($sql);
															?><tr>
																<td data-title="Nombre"><?php echo $Place['Name']; ?></td>
																<td data-title="Dispositivo"><?php echo $Platform; ?></td>
																<td data-title="Tamaño"><?php echo $Size; ?></td>
																<td data-title="Default"><?php echo $Default; ?></td>
																<td data-title="Opciones">
																	<ul class="lst-opt">
																		<li><a href="edit-placement.php?idp=<?php echo $Place['id']; ?>" class="fa-edit tt-lt" data-toggle="tooltip" title="" data-original-title="Editar Placement"></a></li>
																		<li><a href="edit-bidder.php?idb=<?php echo $idB; ?>&d=<?php echo $Place['id']; ?>" onclick="return confirm('¿Esta seguro de eliminar este Placement?')" class="fa-trash-o tt-lt" data-toggle="tooltip" title="" data-original-title="Eliminar Placement"></a></li>
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
												<li class="b-rt"><a href="add-placement.php?idb=<?php echo $idB; ?>" class="fa-plus-circle">Añadir Nuevo Placement</a></li>
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
		

			var table = $('#tbl-estats').DataTable({
				"columnDefs": [{ targets: 'no-sort', orderable: true }],
				"bInfo" : false, 
				"paging": false,
				"lengthChange": false,
				"searching": true,
				"dom": 'lrtip',
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
				}
    		});
    		
    		$('#display').change(function(){
				table.column(1).search(this.value).draw();
			});
			
			$('#searchPlacement').on( 'keyup', function () {
				table.column(0).search(this.value).draw();
			});
		
	});
	
	</script>
</body>
</html>
</html><?php
			}else{
				header('Location: bidders.php');
				exit(0);
			}
		}else{
			header('Location: bidders.php');
			exit(0);
		}
	}else{
		header('Location: index.php');
		exit(0);
	}
?>