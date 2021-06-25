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
	
	$Reloads = 5;
	
	date_default_timezone_set('UTC');
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
						<div class="titl">Control Display</div>
					</div>
					<div class="bx-bd">
						<div class="bx-pd">
							<div class="cls c-fx">
								
							</div>

							<!--<table>-->
							<div class="tbl-cn">
								<table id="tbl-estats">
									<thead>
										<tr>
											<th>Hora</th>
											<th>General</th>
											<th>Fillrate</th>
											<th>Criteo</th>
											<th>AppNexus</th>
											<th>AOL</th>
											<th>Smart</th>
											<th>PulsePoint</th>
											<th>Pubmatic</th>
										</tr>
									<tbody><?php
									

									//echo date('H:i:s');
									$FHour = time() - (3600 * 25);
									$FSecond = strtotime(date('Y-m-d H:00:00', $FHour));
									$Now = time();
									$Correct = 0;
									
									for($TH = $FSecond; $TH <= $Now; $TH = $TH + 3600){
										$Last = $TH + 3599;
										
										
										$ThisHour['criteo']['Pujas'] = 0;
										$ThisHour['criteo']['Win'] = 0;
										$ThisHour['criteo']['SUMCPM'] = 0;
										$ThisHour['criteo']['Rev'] = 0;
										$Promedio['criteo'] = 0;
										
										$ThisHour['appnexus']['Pujas'] = 0;
										$ThisHour['appnexus']['Win'] = 0;
										$ThisHour['appnexus']['SUMCPM'] = 0;
										$ThisHour['appnexus']['Rev'] = 0;
										$Promedio['appnexus'] = 0;
										
										$ThisHour['aol']['Pujas'] = 0;
										$ThisHour['aol']['Win'] = 0;
										$ThisHour['aol']['SUMCPM'] = 0;
										$ThisHour['aol']['Rev'] = 0;
										$Promedio['aol'] = 0;
										
										$ThisHour['pulsepoint']['Pujas'] = 0;
										$ThisHour['pulsepoint']['Win'] = 0;
										$ThisHour['pulsepoint']['SUMCPM'] = 0;
										$ThisHour['pulsepoint']['Rev'] = 0;
										$Promedio['pulsepoint'] = 0;
										
										$ThisHour['smartadserver']['Pujas'] = 0;
										$ThisHour['smartadserver']['Win'] = 0;
										$ThisHour['smartadserver']['SUMCPM'] = 0;
										$ThisHour['smartadserver']['Rev'] = 0;
										$Promedio['smartadserver'] = 0;
										
										$ThisHour['pubmatic']['Pujas'] = 0;
										$ThisHour['pubmatic']['Win'] = 0;
										$ThisHour['pubmatic']['SUMCPM'] = 0;
										$ThisHour['pubmatic']['Rev'] = 0;
										$Promedio['pubmatic'] = 0;
										
										$formatLoads = 0;
										$Impressions = 0;
										$Revenue = 0;
										for($i = 0; $i <= $Reloads; $i++){
											$ImpressionsA[$i] = 0;
											$FillrateA[$i] = 0;
										}
										$sql = "SELECT id FROM " . PREBID_IMPRESION . " WHERE Time BETWEEN '$TH' AND '$Last'";
										$query = $db->query($sql);
										if($db->num_rows($query) > 0){
											while($Imp = $db->fetch_array($query)){
												$formatLoads++;
												
												$sql = "SELECT * FROM " . PREBID_BIDS . " WHERE idImpesion = '" . $Imp['id'] . "'";
												$query2 = $db->query($sql);
												if($db->num_rows($query2) > 0){
													while($Bid = $db->fetch_array($query2)){
														//print_r($Bid);
														//exit(0);
														$ThisHour[$Bid['Bidder']]['Pujas'] = $ThisHour[$Bid['Bidder']]['Pujas'] + 1;
														if($Bid['Winner'] == 1){
															$ThisHour[$Bid['Bidder']]['Win'] = $ThisHour[$Bid['Bidder']]['Win'] + 1;
															$ThisHour[$Bid['Bidder']]['Rev'] = $ThisHour[$Bid['Bidder']]['Rev'] + $Bid['CPM'];
															$Impressions++;
															
															$ImpressionsA[$Bid['Try']] = $ImpressionsA[$Bid['Try']] + 1;

															if($Bid['Currency'] == 'EUR'){
																$Revenue += ($Bid['CPM'] * 1.15);
															}else{
																$Revenue += $Bid['CPM'];
															}
														}
														$ThisHour[$Bid['Bidder']]['SUMCPM'] = $ThisHour[$Bid['Bidder']]['SUMCPM'] + $Bid['CPM'];
														
													}
												}
											}
										}
										
										$eCPM = $Revenue / $formatLoads;
										$Revenue = $Revenue / 1000;
										$Fillrate = ($Impressions / $formatLoads) * 100;
										foreach($ImpressionsA as $Try => $Imp){
											$FillrateA[$Try] = ($Imp / $formatLoads) * 100;
										}
										
										$Promedio['criteo'] = $ThisHour['criteo']['SUMCPM'] / $ThisHour['criteo']['Pujas'];
										$Promedio['appnexus'] = $ThisHour['appnexus']['SUMCPM'] / $ThisHour['appnexus']['Pujas'];
										$Promedio['aol'] = $ThisHour['aol']['SUMCPM'] / $ThisHour['aol']['Pujas'];
										$Promedio['smartadserver'] = $ThisHour['smartadserver']['SUMCPM'] / $ThisHour['smartadserver']['Pujas'];
										$Promedio['pulsepoint'] = $ThisHour['pulsepoint']['SUMCPM'] / $ThisHour['pulsepoint']['Pujas'];
										//print_r($ThisHour);
										//exit(0);
										?><tr>
											<td data-title="Hora"><?php echo date('d/m H:i', $TH + $Correct); ?></td>
											<td data-title="formatLoads">
												Format Loads: <?php echo $formatLoads; ?>
												<br/>Impresiones: <?php echo $Impressions; ?>
												<br/>Revenue: <?php echo number_format($Revenue, 2, '.', ','); ?>$
												<br/>eCPM: <?php echo number_format($eCPM, 2, '.', ','); ?>$
											</td>
											<td>
												Fillrate: <?php echo number_format($Fillrate, 2, '.', ','); ?>% - 
												Carga: <?php echo number_format($FillrateA[0], 2, '.', ','); ?>%
												<br/>
												<?php
												for($i = 0; $i <= $Reloads; $i++){
													if($i > 0){
														?>Try <?php echo $i; ?>: <?php echo number_format($FillrateA[$i], 2, '.', ','); ?>% - <?php
														if($i % 3 == 0){
															?><br/><?php
														}
													}
												}	
													
												?>
											</td>
											<td data-title="Criteo">
												Pujas: <?php echo $ThisHour['criteo']['Pujas'] ?>
												<br/>CPM Av.: <?php echo number_format($Promedio['criteo'], 2, '.', ','); ?>€
												<br/>Ganadas: <?php echo $ThisHour['criteo']['Win']; ?>
												<br/>Revenue: <?php echo number_format($ThisHour['criteo']['Rev'] / 1000, 2, '.', ','); ?>€
											</td>
											<td data-title="AppNexus">
												Pujas: <?php echo $ThisHour['appnexus']['Pujas'] ?>
												<br/>CPM Av.: <?php echo number_format($Promedio['appnexus'], 2, '.', ','); ?>$
												<br/>Ganadas: <?php echo $ThisHour['appnexus']['Win']; ?>
												<br/>Revenue: <?php echo number_format($ThisHour['appnexus']['Rev'] / 1000, 2, '.', ','); ?>$
											</td>
											<td data-title="AOL">
												Pujas: <?php echo $ThisHour['aol']['Pujas'] ?>
												<br/>CPM Av.: <?php echo number_format($Promedio['aol'], 2, '.', ','); ?>$
												<br/>Ganadas: <?php echo $ThisHour['aol']['Win']; ?>
												<br/>Revenue: <?php echo number_format($ThisHour['aol']['Rev'] / 1000, 2, '.', ','); ?>$
											</td>
											<td data-title="Smart">
												Pujas: <?php echo $ThisHour['smartadserver']['Pujas'] ?>
												<br/>CPM Av.: <?php echo number_format($Promedio['smartadserver'], 2, '.', ','); ?>$
												<br/>Ganadas: <?php echo $ThisHour['smartadserver']['Win']; ?>
												<br/>Revenue: <?php echo number_format($ThisHour['smartadserver']['Rev'] / 1000, 2, '.', ','); ?>$
											</td>
											<td data-title="PulsePoint">
												Pujas: <?php echo $ThisHour['pulsepoint']['Pujas'] ?>
												<br/>CPM Av.: <?php echo number_format($Promedio['pulsepoint'], 2, '.', ','); ?>$
												<br/>Ganadas: <?php echo $ThisHour['pulsepoint']['Win']; ?>
												<br/>Revenue: <?php echo number_format($ThisHour['pulsepoint']['Rev'] / 1000, 2, '.', ','); ?>$
											</td>
											<td data-title="Pubmatic">
												Pujas: <?php echo $ThisHour['pubmatic']['Pujas'] ?>
												<br/>CPM Av.: <?php echo number_format($Promedio['pubmatic'], 2, '.', ','); ?>$
												<br/>Ganadas: <?php echo $ThisHour['pubmatic']['Win']; ?>
												<br/>Revenue: <?php echo number_format($ThisHour['pubmatic']['Rev'] / 1000, 2, '.', ','); ?>$
											</td>												
										</tr><?php
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

	
</body>
</html>