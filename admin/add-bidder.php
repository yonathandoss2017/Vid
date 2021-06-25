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
		$Name = '';
		$Code = '';
		$PlacementParam = '';
		$FloorParam = '';
		$idBidderParam = '';
		$idBidder = '';
			
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
				$Time = time();	
					
				$sql = "INSERT INTO " . BIDDERS . " (Name, Code, PlacementParam, FloorParam, idBidderParam, idBidder, Active, Time) VALUES ('$Name', '$Code', '$PlacementParam', '$FloorParam', '$idParam', '$idBidder', '1', '$Time')";
				$db->query($sql);
				$bidId = mysqli_insert_id($db->link);
				
				header('Location: edit-bidder.php?idb=' . $bidId);
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
								<div class="titl">Añadir Bidder</div>
							</div>
							<div class="bx-bd">
								<div class="bx-pd">
									<div id="formadd">
									<form action="" method="post" class="frm-adrsit">
										<div class="bx-hd dfl b-fx">
											<div class="titl">Añadir Bidder</div>
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
											<input type="submit" class="fa-save" value="Crear Nuevo Bidder" name="save" /> 	
										</div>
									</form>
									</div>
								</div>
								
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
    <script>
	    
	jQuery(document).ready(function($){
	});
	
	</script>
</body>
</html>
</html><?php
	}else{
		header('Location: index.php');
		exit(0);
	}
?>