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
	}
	$idAccM = $_SESSION['idAdmin'];
	
	$idCurrency = 1;
	if($idCurrency == 2){
		$DEb = '€';
		$DE = '';
	}else{
		$DEb = '';
		$DE = '$';
	}

	$sql = "SELECT * FROM " . TAGS . "";
	$query = $db->query($sql);
	$TagList = array();
	while($Tag = $db->fetch_array($query)){
		$TagList[$Tag['id']]['RevenueType'] = $Tag['RevenueType'];
		$TagList[$Tag['id']]['Revenue'] = $Tag['Revenue'];
		$TagList[$Tag['id']]['PlatformType'] = $Tag['PlatformType'];
	}

	$Today = date('Y-m-d');
	$Yesterday = date('Y-m-d', time() - 86400);
	$RevenueToday = 0;
	$RevenueYesterday = 0;
	$RevenueThisMonth = 0;
	
	//REVENUE YESTERDAY
	if($_SESSION['Type'] == 3){
		$sql = "SELECT idTag, SUM(Impressions) AS Impressions, SUM(Revenue) AS Revenue, SUM(Coste) AS Coste FROM " . STATS .  " WHERE Date = '$Yesterday' GROUP BY idTag";
	}else{
		$sql = "SELECT idTag, SUM(Impressions) AS Impressions, SUM(Revenue) AS Revenue, SUM(Coste) AS Coste FROM stats INNER JOIN " . USERS .  " ON " . USERS .  ".id = stats.idUser WHERE " . USERS .  ".AccM = '$idAccM' AND stats.Date = '$Yesterday' GROUP BY idTag";
	}
	
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($dataTag = $db->fetch_array($query)){
			$RevenueYesterday += $dataTag['Coste'];
		}
	}
		
	if($_SESSION['Type'] == 3){
		$sql = "SELECT SUM(Amount) FROM " . PREBID_REVENUE .  " WHERE Date = '$Yesterday'";
	}else{
		$sql = "SELECT SUM(" . PREBID_REVENUE .  ".Amount) FROM " . PREBID_REVENUE .  " INNER JOIN " . USERS .  " ON " . USERS .  ".id = " . PREBID_REVENUE .  ".idUser WHERE " . USERS .  ".AccM = '$idAccM' AND " . PREBID_REVENUE .  ".Date = '$Yesterday'";
	}
	
	$RevenueYesterdayDisplay = $db->getOne($sql) / 2;
	$RevenueYesterday = $RevenueYesterday + $RevenueYesterdayDisplay;
	
	$RevenueYesterday = correctCurrency($RevenueYesterday, $idCurrency);
	$RevenueYesterday = number_format($RevenueYesterday, 2, ',', '.');
	$arRT = explode(',',$RevenueYesterday);
	$RevenueYesterdayShow = $DE . $arRT[0] . '<strong>,' . $arRT[1] . $DEb . '</strong>';
	
	//REVENUE TODAY
	if($_SESSION['Type'] == 3){
		$sql = "SELECT idTag, SUM(Impressions) AS Impressions, SUM(Revenue) AS Revenue, SUM(Coste) AS Coste FROM " . STATS .  " WHERE Date = '$Today' GROUP BY idTag";
	}else{
		$sql = "SELECT idTag, SUM(Impressions) AS Impressions, SUM(Revenue) AS Revenue, SUM(Coste) AS Coste FROM " . STATS .  " INNER JOIN " . USERS .  " ON " . USERS .  ".id = " . STATS .  ".idUser WHERE " . USERS .  ".AccM = '$idAccM' AND " . STATS .  ".Date = '$Today' GROUP BY idTag";
	}
	$query = $db->query($sql);
	$ImpressionsToday = 0;
	if($db->num_rows($query) > 0){
		while($dataTag = $db->fetch_array($query)){
			$RevenueToday += $dataTag['Coste'];
			$ImpressionsToday += $dataTag['Impressions'];
		}
	}
	
	if($_SESSION['Type'] == 3){
		$sql = "SELECT SUM(Amount) FROM " . PREBID_REVENUE .  " WHERE Date = '$Today'";
	}else{
		$sql = "SELECT SUM(" . PREBID_REVENUE .  ".Amount) FROM " . PREBID_REVENUE .  " INNER JOIN " . USERS .  " ON " . USERS .  ".id = " . PREBID_REVENUE .  ".idUser WHERE " . USERS .  ".AccM = '$idAccM' AND " . PREBID_REVENUE .  ".Date = '$Today'";
	}
	
	$RevenueTodayDisplay = $db->getOne($sql) / 2;
	$RevenueToday = $RevenueToday + $RevenueTodayDisplay;
	
	$RevenueToday = correctCurrency($RevenueToday, $idCurrency);
	if($ImpressionsToday > 0 && $RevenueToday > 0){
		$eCpm = $RevenueToday / $ImpressionsToday * 1000;
		$totaleCpmShow = number_format($eCpm, 2, ',', '.');
		
		$ShoweCpmToday = '<div class="cpm-txt"><span>CPM Actual <strong>'.$DE.$totaleCpmShow.$DEb.' '.'</strong> </span></div>';
	}else{
		$ShoweCpmToday = '';
	}
	$RevenueToday = number_format($RevenueToday, 2, ',', '.');
	$arRT = explode(',',$RevenueToday);
	$RevenueTodayShow = $DE . $arRT[0] . '<strong>,' . $arRT[1] . $DEb . '</strong>';
	
	
	//REVENUE THIS MONTH
	$FirstDay = date('Y-m-') . '01';
	$LastDay = date('Y-m-t');
	
	
	if($_SESSION['Type'] == 3){
		$sql = "SELECT idTag, SUM(Impressions) AS Impressions, SUM(Revenue) AS Revenue, SUM(Coste) AS Coste FROM " . STATS .  " WHERE Date BETWEEN '$FirstDay' AND '$LastDay' GROUP BY idTag";
	}else{
		$sql = "SELECT idTag, SUM(Impressions) AS Impressions, SUM(Revenue) AS Revenue, SUM(Coste) AS Coste FROM " . STATS .  " INNER JOIN " . USERS .  " ON " . USERS .  ".id = " . STATS .  ".idUser WHERE " . USERS .  ".AccM = '$idAccM' AND " . STATS .  ".Date BETWEEN '$FirstDay' AND '$LastDay' GROUP BY idTag";
	}
	
	
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($dataTag = $db->fetch_array($query)){
			$RevenueThisMonth += $dataTag['Coste'];
		}
	}
	if($_SESSION['Type'] == 3){
		$sql = "SELECT SUM(Amount) FROM " . PREBID_REVENUE .  " WHERE Date BETWEEN '$FirstDay' AND '$LastDay'";
	}else{
		$sql = "SELECT SUM(" . PREBID_REVENUE .  ".Amount) FROM " . PREBID_REVENUE .  " INNER JOIN " . USERS .  " ON " . USERS .  ".id = " . PREBID_REVENUE .  ".idUser WHERE " . USERS .  ".AccM = '$idAccM' AND " . PREBID_REVENUE .  ".Date BETWEEN '$FirstDay' AND '$LastDay'";
	}
	$RevenueThisMonthDisplay = $db->getOne($sql) / 2;
	$RevenueThisMonth = $RevenueThisMonth + $RevenueThisMonthDisplay;
	
	$RevenueThisMonth = correctCurrency($RevenueThisMonth, $idCurrency);
	$RevenueThisMonth = number_format($RevenueThisMonth, 2, ',', '.');
	$arRT = explode(',',$RevenueThisMonth);
	$RevenueThisMonthShow = $DE . $arRT[0] . '<strong>,' . $arRT[1] . $DEb . '</strong>';

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
				<!--<cls>-->
				<div class="cls c-fx">
                    <!--<aside>-->
					<aside class="clmc035 c-flx1">					
						<!--<Cuenta de Ingresos>-->						
						<div class="bx-cn bx-shnone">
							<div class="bx-hd dfl b-fx"><!-- bghd-c-->
								<div class="titl">Cuenta de Ingresos</div>
							</div>
							<div class="bx-bd">
								<div class="ngrs-cn">
									<div>
										<div>
											<p class="ttl-2">Hoy</p>
											<div class="0-fx padbt-15">
												<div class="numb-3"><?php echo $RevenueTodayShow; ?></div>
												<?php echo $ShoweCpmToday; ?>
											</div>
										</div>
                                        <div class="ms-cn">
											<div class="a-fx">
											<div class="a-flx1 c-noflx1">
												<p class="ttl-2">Ayer</p>
												<div>
													<div class="numb-2"><?php echo $RevenueYesterdayShow; ?></div>
												</div>
											</div>
										</div>
										</div>

                               
									</div>
									<div class="cngr-ft">
										<div>
											<p class="ttl-3 padtop-20 padbt-15 fuc">Acumulado este mes</p>
											<div class="numb-2 padbt-20"><?php echo $RevenueThisMonthShow; ?> <!--<span class="fa-chevron-circle-up mr1">+23%</span>--></div>
										</div>
									</div>
                                                                        
                                    <br /><br /><br />
								</div>
							</div>
						</div>
						<!--</Cuenta de Ingresos>-->
					</aside>
					<!--</aside>-->
					<!--<main>-->
					<main class="clmc095">
					
					<!--<Estadisticas Avanzadas>-->
						<div class="bx-cn bx-shnone">
							<div class="bx-hd dfl b-fx bghd-e">
								<div class="titl">Últimos 10 días</div>
								<!--<a href="#" class="btn-hd fa-group mat-record_voice_over b-rt btn-nbd">Ir a Referidos</a>-->
                                         
							</div>
							<div class="bx-bd">
								<div class="bx-pd">
									<div class="bx-hd dfl b-fx">
									</div>
									<div>
										<div id="choises"></div>
										<div class="chart-toggle" style="height: 290px; width: 100%;">
											<canvas id="chart-0"></canvas>
										</div>
										<div class="chrt-inf"><strong>Nota:</strong> Las estadisticas se actualizan cada una hora</div>
									</div>
									
								</div>
							</div>
						</div>
						<!--</Estadisticas Avanzadas>-->
						
						
						
					</main>
					<!--<main>-->
					
				</div>
				<!--</cls>-->
				
                                
                 <!--<Control de Estadísticas>-->
				<div class="bx-cn bx-shnone">
					<div class="bx-hd dfl b-fx bghd-b">
						<div class="titl">Control de Estadísticas</div>
						<div class="d-rt a-o2" style="margin: 3px 3px 0 auto;">
							<div class="fs-dropdown slct-hd dropdown" tabindex="0">
								<button type="button" class="fs-dropdown-selected fs-touch-element" data-toggle="dropdown"><?php
						if(isset($_GET['view'])){
							$view = $_GET['view'];
						}else{
							$view = '';
						}
						
						$view1 = '';
						$view2 = '';
						$view3 = '';
						$view4 = '';
						$view5 = '';
						if($view == 2){
							$andv = '&view=2';
							$view2 = ' class="active"';
							$ShowVal = 'Plataforma';
							$ordern = 3;
						}elseif($view == 3){
							$andv = '&view=3';
							$view3 = ' class="active"';
							$ShowVal = 'Zona';
							$ordern = 3;
						}elseif($view == 4){
							$andv = '&view=4';
							$view4 = ' class="active"';
							$ShowVal = 'Fecha';
							$ordern = 3;
						}elseif($view == 5){
							$andv = '&view=5';
							$view5 = ' class="active"';
							$ShowVal = 'Publisher';
							$ordern = 3;
						}else{
							$andv = '&view=1';
							$view1 = ' class="active"';
							$ShowVal = 'Fecha';
							$ordern = 0;
						}

						if(isset($_GET['range'])){
							$range = $_GET['range'];
						}else{
							$range = '';
						}
						
						$dfrom = '';
						$dto = '';
						if($range == 'today'){
							echo 'Estádisticas de Hoy';
							$andr = 'range=today&';
						}elseif($range == 'yesterday'){
							echo 'Estádisticas de Ayer';
							$andr = 'range=yesterday&';
						}elseif($range == 'lastmonth'){
							echo 'Estádisticas último Mes';
							$andr = 'range=lastmonth&';
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
								$andr = '';
							}
							if($customedates){
								echo $dfrom . ' - ' . $dto;
								$andr = 'range=custom&dfrom='.$dfrom.'&dto='.$dto.'&';
							}
						}else{
							echo 'Estadísticas mes Actual';
							$andr = '';
						}
						?></button>
								<div class="dropdown-menu">											
									<div class="fs-dropdown-options bx-cn">
										<span class="fs-dropdown-group">Seleccionar Fecha</span>
											<button type="button" class="fs-dropdown-item" data-value="1" onclick="location.href='stats.php?range=today<?php echo $andv; ?>'">Estádisticas de Hoy</button>
											<button type="button" class="fs-dropdown-item" data-value="2" onclick="location.href='stats.php?range=yesterday<?php echo $andv; ?>'">Estádisticas de Ayer</button>
											<button type="button" class="fs-dropdown-item" data-value="3" onclick="location.href='stats.php?range=thismonth<?php echo $andv; ?>'">Estádisticas mes Actual</button>
											<button type="button" class="fs-dropdown-item" data-value="4" onclick="location.href='stats.php?range=lastmonth<?php echo $andv; ?>'">Estádisticas último Mes</button>
											<div class="frm-fltr">
												<form action="stats.php?range=custom<?php echo $andv; ?>" method="post">
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
							<ul class="lst-tbs b-fx mb2">
								<li<?php echo $view1; ?>><a href="stats.php?<?php echo $andr; ?>view=1">Datos</a></li>
								<li<?php echo $view2; ?>><a href="stats.php?<?php echo $andr; ?>view=2" class="fa-android">Plataformas</a></li>
								<li<?php echo $view3; ?>><a href="stats.php?<?php echo $andr; ?>view=3" class="fa-globe">Zonas</a></li>
								<li<?php echo $view5; ?>><a href="stats.php?<?php echo $andr; ?>view=5" class="fa-globe">Publisher</a></li>
							</ul>						
							<!--<table>-->
							<div class="tbl-cn">
								<table id="tbl-estats">
									<thead>
										<tr>
											<th><?php echo $ShowVal; ?></th>
											<th>Impresiones</th>
											<th>eCPM</th>
											<th>Revenue</th>
											<th>Coste</th>
											<th>Profit</th>
											<th>Gross margin %</th>
										</tr>
									</thead>
									
									<tbody>
										<?php
											//echo 'HERE:';
											if($range == 'today'){
												$FirstDay = date('Y-m-d');
												$LastDay = date('Y-m-d');
											}elseif($range == 'yesterday'){
												$FirstDay = date('Y-m-d',time() - 86400);
												$LastDay = date('Y-m-d',time() - 86400);
											}elseif($range == 'lastmonth'){
												$FirstDay = date('Y-m-01',strtotime("-1 month"));
												$LastDay = date('Y-m-t',strtotime("-1 month"));
											}elseif($range == 'custom'){
												$arDfrom = explode('.',$dfrom);
												$FirstDay = $arDfrom[2] . '-' . $arDfrom[1] . '-' . $arDfrom[0];
												$arDto = explode('.',$dto);
												$LastDay = $arDto[2] . '-' . $arDto[1] . '-' . $arDto[0];
											}else{
												$FirstDay = date('Y-m-01');
												$LastDay = date('Y-m-d', time() - 86400);
											}
											//echo 'LS';
											
											$TotalImpressions = 0;
											$TotalRevenue = 0;
											$TotalCoste = 0;
											
											if($view == 2){
												$RevenuesByPT[1] = 0;
												$RevenuesByPT[2] = 0;
												$RevenuesByPT[3] = 0;
												$RevenuesByPT[4] = 0;
												$ImpressionsByPt[1] = 0;
												$ImpressionsByPt[2] = 0;
												$ImpressionsByPt[3] = 0;
												$ImpressionsByPt[4] = 0;
												$CosteByPT[1] = 0;
												$CosteByPT[2] = 0;
												$CosteByPT[3] = 0;
												$CosteByPT[4] = 0;
												
												if($_SESSION['Type'] == 3){
													$sql = "SELECT idTag, SUM(Impressions) AS Impressions, SUM(Revenue) AS Revenue, SUM(Coste) AS Coste FROM " . STATS .  " WHERE Date BETWEEN '$FirstDay' AND '$LastDay' GROUP BY idTag";
												}else{
													$sql = "SELECT idTag, SUM(Impressions) AS Impressions, SUM(Revenue) AS Revenue, SUM(Coste) AS Coste FROM stats INNER JOIN users ON users.id = stats.idUser WHERE users.AccM = '$idAccM' AND stats.Date BETWEEN '$FirstDay' AND '$LastDay' GROUP BY idTag";
												}
												
												$query = $db->query($sql);
												if($db->num_rows($query) > 0){
													while($dataTag = $db->fetch_array($query)){
														$CosteThis = $dataTag['Coste'];
														//$RevenueThis = correctCurrency($RevenueThis, $idCurrency);
														$RevenuesByPT[$TagList[$dataTag['idTag']]['PlatformType']] += $dataTag['Revenue'];
														$CosteByPT[$TagList[$dataTag['idTag']]['PlatformType']] += $CosteThis;
														$ImpressionsByPt[$TagList[$dataTag['idTag']]['PlatformType']] += $dataTag['Impressions'];
													}
												}	
												
												for($pla = 1; $pla <= 4; $pla++){
													if($pla <= 2){
														if($pla == 1){
															$isMobile = 0;
														}else{
															$isMobile = 1;
														}
														
														if($_SESSION['Type'] == 3){
															$sql = "SELECT SUM(Amount) FROM " . PREBID_REVENUE .  " WHERE Mobile = '$isMobile' AND Date BETWEEN '$FirstDay' AND '$LastDay'";
														}else{
															$sql = "SELECT SUM(Amount) FROM " . PREBID_REVENUE .  " INNER JOIN " . USERS .  " ON " . USERS .  ".id = " . PREBID_REVENUE .  ".idUser WHERE " . USERS .  ".AccM = '$idAccM' AND " . STATS . ".Mobile = '$isMobile' AND " . STATS .  ".Date BETWEEN '$FirstDay' AND '$LastDay'";
														}
														
														$RevenueDisplay = $db->getOne($sql);
														$CosteDisplay = $RevenueDisplay / 2;
														$RevenuesByPT[$pla] += $RevenueDisplay;
														$CosteByPT[$pla] += $CosteDisplay;
													}
													
													if($pla == 1){
														$PlaType = 'Desktop';
													}elseif($pla == 2){
														$PlaType = 'Mobile Web';
													}elseif($pla == 3){
														$PlaType = 'Mobile App';
													}elseif($pla == 4){
														$PlaType = 'CTV';
													}
													if($ImpressionsByPt[$pla] > 0 && $RevenuesByPT[$pla] > 0){
														$eCpm = $RevenuesByPT[$pla] / $ImpressionsByPt[$pla] * 1000;
														$eCpmShow = $DE . number_format($eCpm, 2, ',', '.') . $DEb;
													}else{
														$eCpmShow = 'NA';
													}
													$Profit = $RevenuesByPT[$pla] - $CosteByPT[$pla];
													if($RevenuesByPT[$pla] > 0){
														$Gross = $Profit / $RevenuesByPT[$pla] * 100;		
													}else{
														$Gross = 0;
													}
													
													?><tr>
														<td data-title="Fecha"> <?php echo $PlaType;?></td>
														<td data-title="Impresiones"><?php echo $ImpressionsByPt[$pla]; ?></td>
														<td data-title="eCPM"><?php echo $eCpmShow; ?></td>
														<td data-title="Ganancias"><strong><span><?php echo $DE . number_format($RevenuesByPT[$pla], 2, ',', '') . $DEb; ?></span></strong></td>
														<td data-title="Coste"><strong><span><?php echo $DE . number_format($CosteByPT[$pla], 2, ',', '') . $DEb; ?></span></strong></td>
														<td data-title="Profit"><strong><span><?php echo $DE . number_format($Profit, 2, ',', '') . $DEb; ?></span></strong></td>
														<td data-title="Gross"><strong><span><?php echo number_format($Gross, 2, ',', ''); ?>%</span></strong></td>
													</tr><?php
													$TotalCoste += $CosteByPT[$pla];
													$TotalImpressions += $ImpressionsByPt[$pla];
													$TotalRevenue += $RevenuesByPT[$pla];
												}
												
											}elseif($view == 3){
												if($_SESSION['Type'] == 3){
													$sql = "SELECT idTag, SUM(Impressions) AS Impressions, SUM(Revenue) AS Revenue, SUM(Coste) AS Coste FROM " . STATS .  " WHERE Date BETWEEN '$FirstDay' AND '$LastDay' GROUP BY idTag";
												}else{
													$sql = "SELECT idTag, SUM(Impressions) AS Impressions, SUM(Revenue) AS Revenue, SUM(Coste) AS Coste FROM stats INNER JOIN users ON users.id = stats.idUser WHERE users.AccM = '$idAccM' AND stats.Date BETWEEN '$FirstDay' AND '$LastDay' GROUP BY idTag";
												}
												
												$query = $db->query($sql);
												if($db->num_rows($query) > 0){
													while($dataTag = $db->fetch_array($query)){
														$sql = "SELECT TagName FROM " . TAGS . " WHERE id = '" . $dataTag['idTag'] . "' LIMIT 1";
														$TagName = $db->getOne($sql);
														
														$Coste = $dataTag['Coste'];
														$Revenue = $dataTag['Revenue'];
														$Impressions = $dataTag['Impressions'];
																				
														$Profit = $Revenue - $Coste;
														if($Revenue > 0){
															$Gross = $Profit / $Revenue * 100;		
														}else{
															$Gross = 0;
														}		

														if($Impressions > 0 && $Revenue > 0){
															$eCpm = $Revenue / $Impressions * 1000;
															$eCpmShow = $DE . number_format($eCpm, 2, ',', '.') . $DEb;
														}else{
															$eCpmShow = 'NA';
														}
														?><tr>
															<td data-title="Fecha"> <?php echo $TagName; ?></td>
															<!--<td data-title="Clicks">2,043 <i class="fa-caret-up"></i></td>
															<td data-title="Altas">124 <i class="fa-caret-down"></i></td>-->
															<td data-title="Impresiones"><?php echo $Impressions; ?></td>
															<td data-title="eCPM"><?php echo $eCpmShow; ?><!--<i class="fa-caret-up"></i>--></td>
															<td data-title="Ganancias"><strong><span><?php echo $DE . number_format($Revenue, 2, ',', '') . $DEb; ?></span></strong></td>
															<td data-title="Coste"><strong><span><?php echo $DE . number_format($Coste, 2, ',', '') . $DEb; ?></span></strong></td>
														<td data-title="Profit"><strong><span><?php echo $DE . number_format($Profit, 2, ',', '') . $DEb; ?></span></strong></td>
														<td data-title="Gross"><strong><span><?php echo number_format($Gross, 2, ',', ''); ?>%</span></strong></td>
														</tr><?php
														$TotalCoste += $Coste;
														$TotalImpressions += $Impressions;
														$TotalRevenue += $Revenue;
													}
												}
											}elseif($view == 5){
												if($_SESSION['Type'] == 3){
													$sql = "SELECT * FROM " . USERS . " ORDER BY id DESC";
												}else{
													$sql = "SELECT * FROM " . USERS . " WHERE AccM = '$idAccM' ORDER BY id DESC";
												}
												$query = $db->query($sql);
												if($db->num_rows($query) > 0){
													while($User = $db->fetch_array($query)){
														$idUser = $User['id'];
													
														$sql = "SELECT idTag, SUM(Impressions) AS Impressions, SUM(Revenue) AS Revenue, SUM(Coste) AS Coste FROM " . STATS .  " WHERE idUser = '$idUser' AND Date BETWEEN '$FirstDay' AND '$LastDay' GROUP BY idTag";
														$query2 = $db->query($sql);
														//exit(0);
														$Revenue = 0;
														$Impressions = 0;
														$Coste = 0;
														if($db->num_rows($query2) > 0){
															while($dataTag = $db->fetch_array($query2)){
																/*$Revenuevalue = $TagList[$dataTag['idTag']]['Revenue'];
																if($TagList[$dataTag['idTag']]['RevenueType'] == 1){
																	$RevenueThis = $dataTag['Revenue'] * $Revenuevalue / 100;
																}else{
																	$RevenueThis = $dataTag['Impressions'] * $Revenuevalue;
																}*/
																//print_r($dataTag);
																$Revenue += $dataTag['Revenue'];
																$Coste += $dataTag['Coste'];
																$Impressions += $dataTag['Impressions'];
																
															}
														}
														
														/* REVENUE COSTE DISPLAY */
														$sql = "SELECT SUM(Amount) FROM " . PREBID_REVENUE .  " WHERE idUser = '$idUser' AND Date BETWEEN '$FirstDay' AND '$LastDay'";
														
														$RevenueDisplay = $db->getOne($sql);
														$CosteDisplay = $RevenueDisplay / 2;
														$Revenue = $Revenue + $RevenueDisplay;
														$Coste = $Coste + $CosteDisplay;
														/* FIN REVENUE COSTE DISPLAY */
														
														
														$TotalImpressions += $Impressions;
														$TotalRevenue += $Revenue;
														$TotalCoste += $Coste;
														
														if($Impressions > 0 && $Revenue > 0){
															$eCpm = $Revenue / $Impressions * 1000;
															$eCpmShow = $DE . number_format($eCpm, 2, ',', '.') . $DEb;
														}else{
															$eCpmShow = 'NA';
														}
														
														$Profit = $Revenue - $Coste;
														if($Revenue > 0){
															$Gross = $Profit / $Revenue * 100;		
														}else{
															$Gross = 0;
														}
														
														?><tr>
															<td data-title="Fecha"> <?php if($User['nick'] != '') { echo $User['nick']; } else { echo $User['user']; } ?></td>
															<!--<td data-title="Clicks">2,043 <i class="fa-caret-up"></i></td>
															<td data-title="Altas">124 <i class="fa-caret-down"></i></td>-->
															<td data-title="Impresiones"><?php echo $Impressions; ?></td>
															<!--<td data-title="% de Conversión">1.18% <i class="fa-caret-up"></i></td>-->
															<td data-title="eCPM"><?php echo $eCpmShow; ?> <!--<i class="fa-caret-up"></i>--></td>
															<td data-title="Ganancias"><strong><span><?php echo $DE . number_format($Revenue, 2, ',', '') . $DEb; ?></span></strong></td>
															<td data-title="Coste"><strong><span><?php echo $DE . number_format($Coste, 2, ',', '') . $DEb; ?></span></strong></td>
														<td data-title="Profit"><strong><span><?php echo $DE . number_format($Profit, 2, ',', '') . $DEb; ?></span></strong></td>
														<td data-title="Gross"><strong><span><?php echo number_format($Gross, 2, ',', ''); ?>%</span></strong></td>
														</tr><?php
													}	
												}
											}else{

												$Time1 = strtotime($LastDay);
												$Time2 = strtotime($FirstDay);
												$Diff = $Time1 - $Time2;
												
												$Days = round($Diff / 86400) + 1;


												for($d = $Days; $d >= 1; $d--){
													$readDate = date('d', $Time1) . ' de ' . readmonth(date('m',$Time1)) . ' ' . date('Y', $Time1);
													$date = date('Y-m-d',$Time1);
													if($_SESSION['Type'] == 3){
														$sql = "SELECT idTag, SUM(Impressions) AS Impressions, SUM(Revenue) AS Revenue, SUM(Coste) AS Coste FROM " . STATS .  " WHERE Date = '$date' GROUP BY idTag";
													}else{
														$sql = "SELECT idTag, SUM(Impressions) AS Impressions, SUM(Revenue) AS Revenue, SUM(Coste) AS Coste FROM " . STATS .  " INNER JOIN " . USERS .  " ON " . USERS .  ".id = " . STATS .  ".idUser WHERE " . USERS .  ".AccM = '$idAccM' AND " . STATS .  ".Date = '$date' GROUP BY idTag";
													}
													$query = $db->query($sql);
													$Impressions = 0;
													$Revenue = 0;
													$Coste = 0;
													//print_r($TagList);
													if($db->num_rows($query) > 0){
														while($dataTag = $db->fetch_array($query)){															
															$Impressions += $dataTag['Impressions'];
															//$Coste += $ThisRevenue;
															$Coste += $dataTag['Coste'];
															$Revenue += $dataTag['Revenue'];
														}
													}

													//$ThisRevenue = correctCurrency($ThisRevenue, $idCurrency);
													
													/* REVENUE COSTE DISPLAY */
													if($_SESSION['Type'] == 3){
														$sql = "SELECT SUM(Amount) FROM " . PREBID_REVENUE .  " WHERE Date = '$date'";
													}else{
														$sql = "SELECT SUM(" . PREBID_REVENUE .  ".Amount) FROM " . PREBID_REVENUE .  " INNER JOIN " . USERS .  " ON " . USERS .  ".id = " . PREBID_REVENUE .  ".idUser WHERE " . USERS .  ".AccM = '$idAccM' AND " . PREBID_REVENUE .  ".Date = '$date'";
														//echo "<!--" . $sql . "-->";
													}
													
													$RevenueDisplay = $db->getOne($sql);
													$CosteDisplay = $RevenueDisplay / 2;
													$Revenue = $Revenue + $RevenueDisplay;
													$Coste = $Coste + $CosteDisplay;
													/* FIN REVENUE COSTE DISPLAY */

													if($Impressions > 0 && $Revenue > 0){
														$eCpm = $Revenue / $Impressions * 1000;
														$eCpmShow = $DE . number_format($eCpm, 2, ',', '.') . $DEb;
													}else{
														$eCpmShow = 'NA';
													}
													
													$TotalImpressions += $Impressions;
													$TotalRevenue += $Revenue;
													$TotalCoste += $Coste;
													
													$Profit = $Revenue - $Coste;
													if($Revenue > 0){
														$Gross = $Profit / $Revenue * 100;		
													}else{
														$Gross = 0;
													}
													
													?><tr>
														<td data-title="Fecha"> <?php echo $readDate;?></td>
														<td data-title="Impresiones"><?php echo number_format($Impressions, 0, ',', '.'); ?></td>
														<td data-title="eCPM"><?php echo $eCpmShow; ?></td>
														<td data-title="Ganancias"><strong><span><?php echo $DE . number_format($Revenue, 2, ',', '') . $DEb; ?></span></strong></td>
														<td data-title="Coste"><strong><span><?php echo $DE . number_format($Coste, 2, ',', '') . $DEb; ?></span></strong></td>
														<td data-title="Profit"><strong><span><?php echo $DE . number_format($Profit, 2, ',', '') . $DEb; ?></span></strong></td>
														<td data-title="Gross"><strong><span><?php echo number_format($Gross, 2, ',', ''); ?>%</span></strong></td>
													</tr><?php
														//echo $Time1 . ': ' . date('d-m-Y', $Time1);
														//echo '<br/>';
														if(date('m-d' ,$Time1) == '03-11'){
															$Time1 = $Time1 - 80000;
														}else{
															$Time1 = $Time1 - 86400;
														}
													
												}
											}
											
											if($TotalImpressions > 0 && $TotalRevenue > 0){
												$eCpm = $TotalRevenue / $TotalImpressions * 1000;
												$totaleCpmShow = $DE . number_format($eCpm, 2, ',', '.') . $DEb;
											}else{
												$totaleCpmShow = 'NA';
											}											
										?>
										
										<tfoot>
										<tr>
											<th colspan="7"><span>Totales: <!--Clicks:-->Impresiones:</span> <strong><?php echo number_format($TotalImpressions, 0, ',', '.'); ?></strong></span> <span class="txspr">·</span> <!--<span>% de Conversiones: <strong>1.19%</strong></span> <span class="txspr">·</span>--> <span>CPM: <strong><?php echo $totaleCpmShow; ?></strong></span> <span class="txspr">·</span> <span>Revenue: <strong><?php echo $DE . number_format($TotalRevenue, 2, ',', '.') . $DEb; ?></strong></span><span class="txspr">·</span> <span>Coste: <strong><?php echo $DE . number_format($TotalCoste, 2, ',', '.') . $DEb; ?></strong></span></th>
										</tr>
										</tfoot>
									</tbody>
								</table>
							</div>
							<!--</table>-->
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
    
    <!-- Grafica -->
    <script src="js/charts/jquery.flot.min.js"></script>
    <script src="js/charts/jquery.flot.resize.min.js"></script>
    
    <script src="js/chart-2.7.1/dist/Chart.bundle.js"></script>
	<script src="js/chart-2.7.1/samples/utils.js"></script>
	<script src="js/chart-2.7.1/samples/charts/area/analyser.js"></script>
	
	<script>
	  $( function() {
		$( "#datepicker" ).datepicker({ dateFormat: 'dd.mm.yy' });
		$( "#datepicker2" ).datepicker({ dateFormat: 'dd.mm.yy' });
	  } );
	</script>

	<?php
		$LastDay = date('Y-m-d');
		$FirstDay = date('Y-m-d',strtotime("-10 days"));
		
		$Time1 = strtotime($LastDay);
		$Time2 = strtotime($FirstDay);
		$Diff = $Time1 - $Time2;
												
		$Days = round($Diff / 86400);
		
		$GraphInfo = array();

		for($d = 1; $d <= $Days; $d++){
			$showdate = date('d-m',$Time2);
			$date = date('Y-m-d',$Time2);
			$sql = "SELECT idTag, SUM(Impressions) AS Impressions, SUM(Revenue) AS Revenue, SUM(Coste) AS Coste FROM " . STATS .  " WHERE Date = '$date' GROUP BY idTag";
			$query = $db->query($sql);
			$ThisImpressions = 0;
			$ThisRevenue = 0;
			
			$GraphInfo[$showdate][1] = 0;
			$GraphInfo[$showdate][2] = 0;
			$GraphInfo[$showdate][3] = 0;
			$GraphInfo[$showdate][4] = 0;
			
			if($db->num_rows($query) > 0){
				while($dataTag = $db->fetch_array($query)){
					/*$Revenuevalue = $TagList[$dataTag['idTag']]['Revenue'];
					if($TagList[$dataTag['idTag']]['RevenueType'] == 1){
						$ThisRevenue = $dataTag['Revenue'] * $Revenuevalue / 100;
					}else{
						$ThisRevenue = $dataTag['Impressions'] * $Revenuevalue;
					}*/
					$ThisRevenue = $dataTag['Coste'];
					$ThisRevenue = correctCurrency($ThisRevenue, $idCurrency);

					$GraphInfo[$showdate][$TagList[$dataTag['idTag']]['PlatformType']] += $ThisRevenue;
					
				}
			}
			$Time2 = $Time2 + 86400;
		}

		//print_r($GraphInfo);

		$Labels = '';
		$Desktop = '';
		$MobileW = '';
		$MobileA = '';
		$CTV = '';
		$coma = '';
		//ksort($GraphInfo);
		foreach($GraphInfo as $Day => $Value){
			$Labels .= "$coma'$Day'";
			
			$Desktop .= "$coma'" . number_format($Value[1],2,'.','') . "'";
			$MobileW .= "$coma'" . number_format($Value[2],2,'.','') . "'";
			$MobileA .= "$coma'" . number_format($Value[3],2,'.','') . "'";
			$CTV .= "$coma'" . number_format($Value[4],2,'.','') . "'";
			
			$coma = ',';
		}
		
		
	?>
	<script type="text/javascript">

		var presets = window.chartColors;
		var utils = Samples.utils;
		var inputs = {
			min: 20,
			max: 80,
			count: 10,
			decimals: 2,
			continuity: 1
		};

		function generateData() {
			//alert(utils.numbers(inputs));
			return utils.numbers(inputs);
		}

		function generateLabels(config) {
			//alert(utils.months({count: inputs.count}));
			//return utils.months({count: inputs.count});
			//return '14-01,15-01,16-01,17-01,18-01,19-01,20-01,21-01';
		}

		utils.srand(42);

		var data = {
			labels: [<?php echo $Labels; ?>],
			datasets: [{
				backgroundColor: utils.transparentize('#c6e69c'),
				borderColor: '#c6e69c',
				data: [<?php echo $Desktop; ?>],
				label: 'Desktop'
			}, {
				backgroundColor: utils.transparentize('#fbb882'),
				borderColor: '#fbb882',
				data: [<?php echo $MobileW; ?>],
				label: 'Mobile Web'
			}, {
				backgroundColor: utils.transparentize('#c7b7e5'),
				borderColor: '#c7b7e5',
				data: [<?php echo $MobileA; ?>],
				label: 'Mobile App'
			}, {
				
				backgroundColor: utils.transparentize('#9de0f5'),
				borderColor: '#9de0f5',
				data: [<?php echo $CTV; ?>],
				label: 'CTV'
			}]
		};

		var options = {
			maintainAspectRatio: false,
			spanGaps: false,
			elements: {
				line: {
					tension: 0.000001
				}
			},
			scales: {
				yAxes: [{
					stacked: false
				}]
			},
			plugins: {
				filler: {
					propagate: true
				},
				samples_filler_analyser: {
					target: 'chart-analyser'
				}
			}
		};

		var chart = new Chart('chart-0', {
			type: 'line',
			data: data,
			options: options
		});


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
				"order": [[ <?php echo $ordern; ?>, "desc" ]],
				"info": false
    		});
		});
    </script>
	
</body>
</html>