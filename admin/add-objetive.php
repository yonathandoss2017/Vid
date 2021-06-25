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
	
	$mesError = '';
	$mesErrorc = '';
	$nPubError = '';
	$nPubErrorc = '';
	$nPubErrors = '';
	$nPubPError = '';
	$nPubPErrorc = '';
	$nPubPErrors = '';
	$nDomError = '';
	$nDomErrorc = '';
	$nDomErrors = '';
	$nDomPError = '';
	$nDomPErrorc = '';
	$nDomPErrors = '';
	$RevError = '';
	$RevErrorc = '';
	$RevErrors = '';
	
	$Mes = '';
	$Publishers = '';
	$PublishersP = '';
	$Domains = '';
	$DomainsP = '';
	$Revenue = '';
	
	if(isset($_POST['save'])){
		$Revenue = $_POST['Revenue'];
		$Revenue = str_replace('$', '', $Revenue);
		$Revenue = str_replace(',', '', $Revenue);
		$Revenue = intval($Revenue);
		$Mes = $_POST['month'];
		$Publishers = $_POST['Publishers'];
		$PublishersP = $_POST['PublishersP'];
		$Domains = $_POST['Domains'];
		$DomainsP = $_POST['DomainsP'];
		$sql = "SELECT COUNT(*) FROM " . OBJETIVES . " WHERE AccM = '$idAccM' AND Mes = '$Mes' AND Deleted = 0";
		if($db->getOne($sql) == 0){
			$Time = time();
			$Date = date('Y-m-d');
			
			$sql = "INSERT INTO " . OBJETIVES . " (AccM, Mes, Publishers, PublishersP, Dominios, DominiosP, Revenue, Time, Date) 
			VALUES ('$idAccM', '$Mes', '$Publishers', '$PublishersP', '$Domains', '$DomainsP', '$Revenue', '$Time', '$Date')";
			$db->query($sql);
			
			header('Location: objetives-manager.php?idam=' . $idAccM);
			exit(0);
		}else{
			$mesError = ' data-error="Ya existe un Objetivo definido para el mes seleccionado."';
			$mesErrorc = ' frm-rrr';
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
				
				<!--<Control de objetivos>-->
				<div class="bx-cn bx-shnone">
					<div class="bx-hd dfl b-fx bghd-e">
						<div class="titl">Añadir Objetivo para <?php echo $AccM['Name']; ?></div>
					</div>
					<div class="bx-bd">
						<div class="bx-pd">
							
									<form action="" method="post" class="frm-adrsit">
										<div class="bx-hd dfl b-fx">
											<div class="titl">Nuevo Objetivo</div>
										</div>
										<div class="clsd-fx">
											<div class="clmd12">
												<!--<Mes>-->
												<div class="frm-group d-fx lbl-lf<?php echo $mesErrorc; ?>">
													<label>Mes</label>
													<div class="d-flx1">
														<label<?php echo $mesError; ?>>
															<select name="month" id="month" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																<optgroup label="Selecciona el Mes"><?php
																		$CurrentM = date('n');
																	?>
																	<option value="<?php echo date('Y-n'); ?>"><?php echo $MonthSpanish[$CurrentM] . date(' Y'); ?></option>
																<?php
																	for($i = 1;  $i <= 12; $i++ ){
																		$ThisM = date('n',strtotime("+$i month"));
																		$ThisY = date('Y',strtotime("+$i month"));
																		?><option value="<?php echo $ThisY . '-' . $ThisM; ?>"><?php echo $MonthSpanish[$ThisM] . ' ' . $ThisY; ?></option><?php 
																	}
																?>
																</optgroup>
															</select>
														</label>
													</div>
												</div>
												<!--</Mes>-->
											</div>
											<div class="clmd06">
												<!--<Publishers>-->
												<div class="frm-group d-fx lbl-lf<?php echo $nPubErrorc; ?>">
													<label<?php echo $nPubErrors; ?>>Publishers</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $nPubError; ?>>
															<input type="text" name="Publishers" class="numeric2" value="<?php echo $Publishers; ?>" />
														</label>
													</div>
												</div>
												<!--</Publishers>-->
											</div>
											<div class="clmd06">
												<!--<Publishers Premium>-->
												<div class="frm-group d-fx lbl-lf<?php echo $nPubPErrorc; ?>">
													<label<?php echo $nPubPErrors; ?>>Pub. Premium</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $nPubPError; ?>>
															<input type="text" name="PublishersP" class="numeric2" value="<?php echo $PublishersP; ?>" />
														</label>
													</div>
												</div>
												<!--</Publishers Premium>-->
											</div>
											<div class="clmd06">
												<!--<Publishers Premium>-->
												<div class="frm-group d-fx lbl-lf<?php echo $nDomErrorc; ?>">
													<label<?php echo $nDomErrors; ?>>Dominios</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $nDomError; ?>>
															<input type="text" name="Domains" class="numeric2" value="<?php echo $Domains; ?>" />
														</label>
													</div>
												</div>
												<!--</Publishers Premium>-->
											</div>
											<div class="clmd06">
												<!--<Publishers Premium>-->
												<div class="frm-group d-fx lbl-lf<?php echo $nDomPErrorc; ?>">
													<label<?php echo $nDomPErrors; ?>>Dom. Premium</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $nDomPError; ?>>
															<input type="text" name="DomainsP" class="numeric2" value="<?php echo $DomainsP; ?>" />
														</label>
													</div>
												</div>
												<!--</Publishers Premium>-->
											</div>
											<div class="clmd06">
												<!--<Revenue>-->
												<div class="frm-group d-fx lbl-lf<?php echo $RevErrorc; ?>">
													<label<?php echo $RevPErrors; ?>>Revenue</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $RevError; ?>>
															<input type="text" name="Revenue" class="numeric" value="<?php echo $Revenue; ?>" placeholder="$ 0"  data-a-sign="$ " />
														</label>
													</div>
												</div>
												<!--</Revenue>-->
											</div>
										</div>
										
										<div class="botnr-cn">
											<input type="submit" class="fa-save" value="Crear Objetivo" name="save" />
										</div>
									</form>						
							

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
    <script src="js/autoNumeric.js"></script>

   <script>
		jQuery(document).ready(function ($) {

            $('.numeric').autoNumeric('init', {mDec: '0'});
            $('.numeric2').autoNumeric('init', {mDec: '0', aSep: ''});

		});
	</script>

</body>
</html>