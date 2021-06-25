<?php
	session_start();
	define('CONST',1);
	if($_SESSION['Type'] == 1 || $_SESSION['Type'] == 3) {
		if($_SESSION['Type'] == 3){
			if(isset($_GET['idam'])){
				$idAccM = $_GET['idam'];
			}else{
				header('Location: acc-managers.php');
				exit(0);
			}
		}else{
			$idAccM = $_SESSION['idAdmin'];
		}
	}else{
		header('Location: login.php');
		exit(0);
	}
	require('../config.php');
	require('../db.php');
	require('../constantes.php');
	require('../common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	require('libs/pub-managers.lib.php');
	$pm = new PM();
		
	$sql = "SELECT * FROM " . ACC_MANAGERS . " WHERE id = '$idAccM' LIMIT 1";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		$AccM = $db->fetch_array($query);
	}else{
		header('Location: acc-managers.php');
		exit(0);
	}
	
	if(isset($_GET['y'])){
		$Year = intval($_GET['y']);
	}else{
		$Year = false;
	}
	
	$arDate = explode('-',$AccM['Date']);
	$YearD = $arDate[0];
	$MonthD = $arDate[1];
	
?><!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Objetivos de <?php echo $AccM['Name']; ?> - Vidoomy</title>
    <meta name="description" content="Vidoomy">
    <meta name="keywords" content="Vidoomy">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://fonts.googleapis.com/css?family=Cabin:400italic,600italic,700italic,400,600,700' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Lato:300,400,700,900' rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="css/jquery-ui.structure.min.css">
    <link rel="stylesheet" href="css/fa.css">
    <link rel="stylesheet" href="css/css.css">
    <link rel="stylesheet" href="css/thimify.css">
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
						<div class="titl">Objetivos de <?php echo $AccM['Name']; ?> <?php if($Year) { echo "- $Year"; } ?></div>
					</div>
					<div class="bx-bd">
						<div class="bx-pd">
							<?php if($Year) { ?>
								<div class="tbl-cn">
									<table id="tbl-estats">
										<thead>
											<tr>
												<th>Mes</th>
												<th>Dominios</th>
												<th>Dom. Premium</th>
												<th>Publishers</th>
												<th>Pub. Premium</th>
												<th>Revenue</th>
											</tr>
										</thead>
										<tbody><?php
										if($Year == date('Y')){
											$MH = date('n');
										}else{
											$MH = 12;
										}
										
										if($Year == $YearD){
											$MonthDe = $MonthD;
										}else{
											$MonthDe = 1;
										}
											
										for($M = $MH; $M >= $MonthDe; $M--){
											
											$Period = $Year . '-' . $M;
											
											$Revenue = $pm->getMonthRevenue($idAccM, $Period);
											
											$sql = "SELECT * FROM " . OBJETIVES . " WHERE AccM = '$idAccM' AND Mes = '$Period' LIMIT 1";
											$query = $db->query($sql);
											if($db->num_rows($query) > 0){
												$Obj = $db->fetch_array($query);
											}else{
												$sql = "SELECT * FROM " . OBJETIVES . " WHERE id = '1' LIMIT 1";
												$query = $db->query($sql);
												$Obj = $db->fetch_array($query);
											}
												
											$RegularDomains = $pm->getAccMDomains($idAccM, $Period, false);
											$PremiumDomains = $pm->getAccMDomains($idAccM, $Period, true);
											
											$RegularPublishers = $pm->getAccMPublishers($idAccM, $Period, false);
											$PremiumPublishers = $pm->getAccMPublishers($idAccM, $Period, true);
											
											$LostDomains = $pm->getAccMLDomains($idAccM, $Period);
											$LostPublishers = $pm->getAccMLPublishers($idAccM, $Period);
											
											$ObjRevenue = $Obj['Revenue'];
											$PerRevenue = number_format($Revenue * 100 / $ObjRevenue, 2, '.', ',');
											
											$ObjPublishers = $Obj['Publishers'];
											$ObjPublishersP = $Obj['PublishersP'];
											$ObjDominios = $Obj['Dominios'];
											$ObjDominiosP = $Obj['DominiosP'];
											
											$PubPer = $RegularPublishers * 100 / $ObjPublishers;
											$PPubPer = $PremiumPublishers * 100 / $ObjPublishersP;
											$DomPer = $RegularDomains * 100 / $ObjDominios;
											$PDomPer = $PremiumDomains * 100 / $ObjDominiosP;
										
											?><tr>
												<td data-title="Mes"><a href="objetives.php?idam=<?php echo $AccM['id']; ?>&p=<?php echo $Period; ?>"><?php echo $MonthSpanish[$M]; ?></a></td>
												<td data-title="Dominios"><?php echo $DomPer; ?>%</td>
												<td data-title="Dominios Premium"><?php echo $PDomPer; ?>%</td>
												<td data-title="Publishers"><?php echo $PubPer; ?>%</td>
												<td data-title="Publishers Premium"><?php echo $PPubPer; ?>%</td>
												<td data-title="Revenue"><?php echo $PerRevenue; ?>%</td>
											</tr><?php
											
										}
	
										?>
										</tbody>
									</table>
								</div>							
							<?php } else { ?>
							
								<div class="tbl-cn">
									<table id="tbl-estats">
										<thead>
											<tr>
												<th>Año</th>
											</tr>
										</thead>
										<tbody><?php
											
										$YH = date('Y');
											
										for($Y = $YearD; $Y <= $YH; $Y++){
											
											?><tr>
												<td data-title="Año"><a href="objetives-list.php?idam=<?php echo $AccM['id']; ?>&y=<?php echo $Y; ?>"><?php echo $Y; ?></a></td>
											</tr><?php
										}
	
										?>
										</tbody>
									</table>
								</div>							

							
							<?php } ?>
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
    
</body>
</html>