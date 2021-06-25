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
	
	$NoGlobal = true;	
	
	if(isset($_GET['ido'])){
		$idO = intval($_GET['ido']);
	}else{
		header('Location: objetive-manager.php?idam=' . $idAccM);
		exit(0);
	}
	
	if(isset($_GET['idam'])){
		$idAccM = intval($_GET['idam']);
	}elseif($idO == 1){
		$NoGlobal = false;	
	}else{
		header('Location: acc-managers.php');
		exit(0);
	}
	
	if($NoGlobal){
		$sql = "SELECT * FROM " . ACC_MANAGERS . " WHERE id = '$idAccM' LIMIT 1";
		$query = $db->query($sql);
		if($db->num_rows($query) > 0){
			$AccM = $db->fetch_array($query);
		}else{
			header('Location: acc-managers.php');
			exit(0);
		}
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
	
	if(isset($_POST['save'])){
		$Publishers = $_POST['Publishers'];
		$PublishersP = $_POST['PublishersP'];
		$Domains = $_POST['Domains'];
		$DomainsP = $_POST['DomainsP'];
		$Revenue = $_POST['Revenue'];
		$Revenue = trim(str_replace('$','',$Revenue));
		$Revenue = str_replace(',','',$Revenue);
			
		$sql = "UPDATE " . OBJETIVES . " SET Publishers = '$Publishers', PublishersP = '$PublishersP', Dominios = '$Domains', DominiosP = '$DomainsP', Revenue = '$Revenue' WHERE id = '$idO' LIMIT 1";
		$db->query($sql);
		
		if($NoGlobal){
			header('Location: objetives-manager.php?idam=' . $idAccM);
		}else{
			header('Location: acc-managers.php');
		}
		exit(0);
	}
	
	$sql = "SELECT * FROM " . OBJETIVES . " WHERE id = '$idO' LIMIT 1";
	$query = $db->query($sql);
	if($db->num_rows($query) == 0){
		header('Location: objetives-manager.php?idam=' . $idAccM);
		exit(0);
	}
	$Obj = $db->fetch_array($query);
	
	$Publishers = $Obj['Publishers'];
	$PublishersP = $Obj['PublishersP'];
	$Domains = $Obj['Dominios'];
	$DomainsP = $Obj['DominiosP'];
	$Revenue = $Obj['Revenue'];
	
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
						<div class="titl">Editar Objetivo <?php if($NoGlobal){ ?>de <?php echo $AccM['Name']; ?><?php } else { ?> Global por Defecto<?php } ?></div>
					</div>
					<div class="bx-bd">
						<div class="bx-pd">
							
									<form action="" method="post" class="frm-adrsit">
										<div class="bx-hd dfl b-fx">
											<div class="titl">Editar Objetivo</div>
										</div>
										<div class="clsd-fx">
											<?php if($NoGlobal){ ?>
											<div class="clmd12">
												<!--<Mes>-->
												<div class="frm-group d-fx lbl-lf<?php echo $mesErrorc; ?>">
													<label>Mes</label>
													<div class="d-flx1">
														<label<?php echo $mesError; ?>>
															<select name="month" id="month" disabled="disabled">
																<optgroup label="Selecciona el Mes"><?php
																		$arM = explode('-',$Obj['Mes']);
																	?>
																	<option><?php echo $MonthSpanish[$arM[0]] . ' ' . $arM[1]; ?></option>
																</optgroup>
															</select>
														</label>
													</div>
												</div>
												<!--</Mes>-->
											</div>
											<?php } ?>
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
											<input type="submit" class="fa-save" value="Modificar Objetivo" name="save" />
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