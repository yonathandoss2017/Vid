<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('config.php');
	require('constantes.php');
	require('db.php');
	require('common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	header('Location: stats.php');
	exit(0);
	
	if(isset($_POST['user'])){
		$user = my_clean($_POST['user']);
		if(strlen($_POST['password']) == 32){
			$pass = my_clean($_POST['password']);
		}else{
			$pass = md5($_POST['password']);
		}
				
		$sql = "SELECT id FROM " . USERS . " WHERE user = '$user' AND password = '$pass' LIMIT 1";
		$logID = $db->getOne($sql);
		if($logID > 0){
			$_SESSION['login'] = $logID;
			if(strlen($_POST['password']) != 32){
				$sql = "UPDATE " . USERS . " SET lastlogin = '" . time() . "' WHERE id = '$logID' LIMIT 1";
				$db->query($sql);
			}
		}else{
			header('Location: index.php?logerror=1');
			exit(0);
		}

	}elseif(@$_SESSION['login'] >= 1){
		
	}else{
		header('Location: index.php');
		exit(0);
	}
	$sql = "SELECT currency, showi FROM " . USERS . " WHERE id = '" . $_SESSION['login'] . "'";
	$query = $db->query($sql);
	$userData = $db->fetch_array($query);
	$idCurrency = $userData['currency'];
	if($userData['showi'] == 1){
		$ShowI = true;
	}else{
		$ShowI = false;
	}
	if($idCurrency == 2){
		$DEb = '€';
		$DE = '';
	}else{
		$DEb = '';
		$DE = '$';
	}

	$sql = "SELECT * FROM " . TAGS . " WHERE idUser = '" . $_SESSION['login'] . "'";
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
	$sql = "SELECT idTag, SUM(Impressions) AS Impressions, SUM(Revenue) AS Revenue FROM " . STATS .  " WHERE idUser = '" . $_SESSION['login'] . "' AND Date = '$Yesterday' GROUP BY idTag";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($dataTag = $db->fetch_array($query)){
			$Revenuevalue = $TagList[$dataTag['idTag']]['Revenue'];
			if($TagList[$dataTag['idTag']]['RevenueType'] == 1){
				$RevenueYesterday += $dataTag['Revenue'] * $Revenuevalue / 100;
			}else{
				$RevenueYesterday += $dataTag['Impressions'] * $Revenuevalue;
			}
		}
	}
	$RevenueYesterday = correctCurrency($RevenueYesterday, $idCurrency);
	$RevenueYesterday = number_format($RevenueYesterday, 2, ',', '.');
	$arRT = explode(',',$RevenueYesterday);
	$RevenueYesterdayShow = $DE . $arRT[0] . '<strong>,' . $arRT[1] . $DEb . '</strong>';
	
	//REVENUE TODAY
	$sql = "SELECT idTag, SUM(Impressions) AS Impressions, SUM(Revenue) AS Revenue FROM " . STATS .  " WHERE idUser = '" . $_SESSION['login'] . "' AND Date = '$Today' GROUP BY idTag";
	$query = $db->query($sql);
	$ImpressionsToday = 0;
	if($db->num_rows($query) > 0){
		while($dataTag = $db->fetch_array($query)){
			$Revenuevalue = $TagList[$dataTag['idTag']]['Revenue'];
			if($TagList[$dataTag['idTag']]['RevenueType'] == 1){
				$RevenueToday += $dataTag['Revenue'] * $Revenuevalue / 100;
			}else{
				$RevenueToday += $dataTag['Impressions'] * $Revenuevalue;
			}
			$ImpressionsToday += $dataTag['Impressions'];
		}
	}
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
	$sql = "SELECT idTag, SUM(Impressions) AS Impressions, SUM(Revenue) AS Revenue FROM " . STATS .  " WHERE idUser = '" . $_SESSION['login'] . "' AND Date BETWEEN '$FirstDay' AND '$LastDay' GROUP BY idTag";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($dataTag = $db->fetch_array($query)){
			$Revenuevalue = $TagList[$dataTag['idTag']]['Revenue'];
			if($TagList[$dataTag['idTag']]['RevenueType'] == 1){
				$RevenueThisMonth += $dataTag['Revenue'] * $Revenuevalue / 100;
			}else{
				$RevenueThisMonth += $dataTag['Impressions'] * $Revenuevalue;
			}
		}
	}
	$RevenueThisMonth = correctCurrency($RevenueThisMonth, $idCurrency);
	$RevenueThisMonth = number_format($RevenueThisMonth, 2, ',', '.');
	$arRT = explode(',',$RevenueThisMonth);
	$RevenueThisMonthShow = $DE . $arRT[0] . '<strong>,' . $arRT[1] . $DEb . '</strong>';

?><!doctype html>
<html lang="es">
<head>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-KDK6GTQ');</script>
<!-- End Google Tag Manager -->
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
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KDK6GTQ"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
	<!--<all>-->
	<div class="all">
	
		<!--<hdcn>-->
		<header class="hdcn">
			<div class="cnt a-fx">
                                
							<div class="logo">
								<div class="on-user dropdown drp-lf hidden visible-xs">
									<a href="#" data-toggle="dropdown">
										<!--<img src="img/cnt/user.jpg" alt="user">-->
										<i class="material-icons menu-hd">menu</i>
									</a>
									<div class="dropdown-menu">
										<div class="bx-cn">
											<div class="bx-hd dfl b-fx">
												<span>Tienes 56 Avisos</span>
											</div>
											<div class="bx-bd">
												<ul class="lst-dpdw">
													<li>
														<a href="#">Mi Cuenta <span>configura tus datos</span><i class="material-icons">settings</i></a>
													</li>
													<li>
														<a href="#">Facturación <span>Realiza tus cobros</span><i class="material-icons">euro_symbol</i></a>
													</li>
													<li>
														<a href="#">Estadisticas <span>Informes avanzados</span><i class="material-icons">pie_chart</i></a>
													</li>
													<li>
														<a href="#">Zonas <span>Da de alta tus webs</span><i class="material-icons">create_new_folder</i></a>
													</li>
													<li>
														<a href="#">Tickets <span>Historial de mensajeria</span><i class="material-icons">message</i></a>
													</li>
												</ul>
											</div>
										</div>
									</div>
								</div>        
								<a href="#"><img src="img/vidoomy-logo.png" class="mh-logo" alt="vidoomy"></a>
							</div>
							
							
				<nav class="mn-user a-flx1">
					<ul class="0-fx">
						<li class="nt-user 0-fx 0-flx1">
                            <?php /*
                            <div class="mj-user dropdown drp-lf">
								<a href="#" class="fa-envelope-o mat-email"><!--<span>Account Manager asignado <span>Marcos Cuesta</span></span>--><i class="lb-num">3</i></a>
								<div class="dropdown-menu">
									<div class="bx-cn">
										<div class="bx-hd dfl 0-fx">
											<span>Tienes 56 Avisos</span>
											<span class="0-rt">Caracteres <small>0/400</small></span>
										</div>
										<div class="bx-bd">
											<form action="#" class="bx-pd frm-sndmj">
												<div class="frm-group">
													<textarea cols="66" rows="6" placeholder="Escrtibe aqui tu mensaje"></textarea>
												</div>
												<div class="dfl b-fx">
													<div class="frm-group b-flx1">
														<input type="file" class="filestyle" data-input="false" data-icon="false" data-buttonName="uplfil" data-buttonText="Adjuntar archivo <span class='fa-file-pdf-o'>.pdf</span> o <span class='fa-file-picture-o'>.jpg</span>">
													</div>
													<div class="frm-group b-rt">
														<button type="submit">Enviar</button>
													</div>
												</div>												
											</form>
										</div>
									</div>
								</div>
							</div>-->
							<!--<div class="dropdown drp-lf">
								<a href="#" class="fa-bell-o mat-bell bt-dpdw" data-toggle="dropdown"><i class="lb-num-b">3</i></a>
								<div class="dropdown-menu">
									<div class="bx-cn">
										<div class="bx-hd dfl b-fx">
											<span>Tienes 56 Avisos</span>
										</div>
										<div class="bx-bd">
											<ul class="lst-dpdw">
												<li>
													<a href="#">Marcos Cuesta <span>Pago realizado con éxito</span> <small>5 min.</small><i class="material-icons">add_alarm</i></a>
												</li>
												<li>
													<a href="#">Marcos Cuesta <span>Pago realizado con éxito</span> <small>5 min.</small><i class="material-icons">add_alarm</i></a>
												</li>
												<li>
													<a href="#">Marcos Cuesta <span>Pago realizado con éxito</span> <small>5 min.</small><i class="material-icons">add_alarm</i></a>
												</li>
												<li>
													<a href="#">Marcos Cuesta <span>Pago realizado con éxito</span> <small>5 min.</small><i class="material-icons">add_alarm</i></a>
												</li>
												<li class="dpdw-f"><a href="#">Ver todos los avisos</a></li>
											</ul>
										</div>
									</div>
								</div>
							</div> */ ?>
                                                        
						</li>
						<li class="op-user 0-fx 0-rt hidden-xs menu-r">
                            <!--<a href="#"><i class="material-icons">settings</i><span>Mi Cuenta</span></a>-->
                            <!--<a href="#"><i class="material-icons">euro_symbol</i><span>Facturación</span></a>-->
                            <a href="#"><i class="material-icons">pie_chart</i><span>Estadisticas</span></a>
                            <!--<a href="#"><i class="material-icons">create_new_folder</i><span>Zonas</span></a>-->
                            <!--<a href="#"><i class="material-icons">message</i><span>Tickets</span></a>-->
												
							<!--<div class="dropdown drp-lf">
								<a href="#" class="fa-envelope-o bt-dpdw" data-toggle="dropdown"><i class="lb-num">3</i></a>
								<div class="dropdown-menu">
									<div class="bx-cn">
										<div class="bx-hd dfl b-fx bghd-b">
											<span>Acceso afiliados</span>
										</div>
										<div class="bx-bd">
											<ul class="lst-dpdw">
												<li>
													<a href="#">Joel cabezapolla <span>Aviso administrativo</span> <small>5 min.</small><i class="fa-user"></i></a>
												</li>
												<li>
													<a href="#">Respuesta Ticket <span>En respuesta a su ticket</span> <small>5 min.</small><i class="fa-envelope-o"></i></a>
												</li>
												<li>
													<a href="#">Respuesta Ticket <span>En respuesta a su ticket</span> <small>5 min.</small><i class="fa-envelope-o"></i></a>
												</li>
												<li>
													<a href="#">Respuesta Ticket <span>En respuesta a su ticket</span> <small>5 min.</small><i class="fa-envelope-o"></i></a>
												</li>
												<li class="dpdw-f"><a href="#">Ver todos los mensajes</a></li>
											</ul>
										</div>
									</div>
								</div>
							</div>-->
							
							<!--<div class="on-user dropdown drp-rt">
								<a href="#" data-toggle="dropdown">
									<img src="img/cnt/user.jpg" alt="user">
									<i class="material-icons menu-hd">menu</i>
								</a>
								<div class="dropdown-menu">
									<div class="bx-cn">
										<div class="bx-hd dfl b-fx">
											<span>Tienes 56 Avisos</span>
										</div>
										<div class="bx-bd">
											<ul class="lst-dpdw">
												<li>
													<a href="#">Mi Cuenta <span>configura tus datos</span><i class="fa-cog"></i></a>
												</li>
												<li>
													<a href="#">Facturación <span>Realiza tus cobros</span><i class="fa-euro"></i></a>
												</li>
												<li>
													<a href="#">Estadisticas <span>Informes avanzados</span><i class="fa-pie-chart"></i></a>
												</li>
												<li>
													<a href="#">Zonas <span>Da de alta tus webs</span><i class="fa-folder"></i></a>
												</li>
												<li>
													<a href="#">Tickets <span>Historial de mensajeria</span><i class="fa-envelope-o"></i></a>
												</li>
											</ul>
										</div>
									</div>
								</div>
							</div>-->
                                                        
                                                        
							
						</li>
					</ul>
				</nav>
			</div>
		</header>
		<!--<hdcn>-->
		
		<!--<bdcn>-->
		<div class="bdcn">
			<div class="cnt">
				<!--<cls>-->
				<div class="cls c-fx">
                    <!--<aside>-->
					<aside class="clmc035 c-flx1">
						<!--<Accesos Rápidos>-->
						<!--<div class="bx-cn">
							<div class="bx-hd dfl b-fx">
								<div class="titl">Accesos Rápidos</div>
							</div>
							<div class="bx-bd">
								<ul class="lst-acrap 0-fx ax06dx03">
									<li><a href="#" class="fa-cog">Mi cuenta</a></li>
									<li><a href="#" class="fa-folder">Zonas</a></li>
									<li><a href="#" class="fa-pie-chart">Estadisticas</a></li>
									<li><a href="#" class="fa-euro">Facturación</a></li>
								</ul>
							</div>
						</div>-->
						<!--</Accesos Rápidos>-->						
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
												<?php if($ShowI){ echo $ShoweCpmToday; } ?>
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
                                    <!--<a href="#" class="btn-hd-ref mat-record_voice_over">Ver Referidos</a>-->
                                                                            
                                                                        
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
								
										<!--<div class="titl">Herramientas</div>-->
										
										<!--<div class="tgs-lnk ctr a-o1">
											<span>Clicks <strong>215</strong></span> <span>Clicks <strong>215</strong></span> <span>Clicks <strong>215</strong></span>
										</div>-->
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
						<div class="d-rt a-o2" style="margin: 3px 3px 0 auto;">																	<div class="fs-dropdown slct-hd dropdown" tabindex="0">
								<button type="button" class="fs-dropdown-selected fs-touch-element" data-toggle="dropdown"><?php
						if(isset($_GET['view'])){
							$view = $_GET['view'];
						}else{
							$view = '';
						}
						
						$view1 = '';
						$view2 = '';
						$view3 = '';
						if($view == 2){
							$andv = '&view=2';
							$view2 = ' class="active"';
						}elseif($view == 3){
							$andv = '&view=3';
							$view3 = ' class="active"';
						}else{
							$andv = '&view=1';
							$view1 = ' class="active"';
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
											<button type="button" class="fs-dropdown-item" data-value="1" onclick="location.href='estadisticas.php?range=today<?php echo $andv; ?>'">Estádisticas de Hoy</button>
											<button type="button" class="fs-dropdown-item" data-value="2" onclick="location.href='estadisticas.php?range=yesterday<?php echo $andv; ?>'">Estádisticas de Ayer</button>
											<button type="button" class="fs-dropdown-item" data-value="3" onclick="location.href='estadisticas.php?range=thismonth<?php echo $andv; ?>'">Estádisticas mes Actual</button>
											<button type="button" class="fs-dropdown-item" data-value="4" onclick="location.href='estadisticas.php?range=lastmonth<?php echo $andv; ?>'">Estádisticas último Mes</button>
											<div class="frm-fltr">
												<form action="estadisticas.php?range=custom<?php echo $andv; ?>" method="post">
													<p>Personaliza las Fechas</p>
													<div class="clsb-fx">
														<div class="clmd06">
															<div class="frm-group">
																<label>Desde</label>
																<div class="d-flx1">
																	<label>
																		<input type="text" name="dfrom" placeholder="17.12.2017" value="<?php echo $dfrom; ?>" />
																	</label>
																</div>
															</div>
														</div>
														<div class="clmd06">
															<div class="frm-group">
																<label>Hasta</label>
																<div class="d-flx1">
																	<label>
																		<input type="text" name="dto" placeholder="25.12.2017" value="<?php echo $dto; ?>" />
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
								<li<?php echo $view1; ?>><a href="estadisticas.php?<?php echo $andr; ?>view=1">Datos</a></li>
								<!--<li><a href="#" class="fa-street-view">Paises</a></li>-->
								<!--<li><a href="#" class="fa-list-alt">Sitios</a></li>-->
								<li<?php echo $view2; ?>><a href="estadisticas.php?<?php echo $andr; ?>view=2" class="fa-android">Plataformas</a></li>
								<!--<li><a href="#" class="fa-flag">Website</a></li>--><?php
								if($ShowI){
								?><li<?php echo $view3; ?>><a href="estadisticas.php?<?php echo $andr; ?>view=3" class="fa-globe">Zonas</a></li><?php
								}
								?><!--<li><a href="#" class="fa-mobile-phone">Compañias</a></li>-->
								<!--<li class="b-rt"><a href="#" class="fa-download mat-get_app">PDF</a></li>-->
							</ul>						
							<!--<table>-->
							<div class="tbl-cn">
								<table id="tbl-estats">
									<thead>
										<tr>
											<th>Fecha</th>
											<!--<th>Clicks</th>
											<th>Altas</th>-->
										<?php if($ShowI){ ?>
											<th>Impresiones</th>
											<!--<th>% de Conversión</th>-->
											<th>eCPM</th>
										<?php } ?>
											<th>Ganancias</th><!-- class="tx-dr"-->
										</tr>
									</thead>
									
									<tbody>
										<?php
											if($range == 'today'){
												$FirstDay = date('Y-m-d');
												$LastDay = date('Y-m-d');
											}elseif($range == 'yesterday'){
												$FirstDay = date('Y-m-d',time() - 86400);
												$LastDay = date('Y-m-d',time() - 86400);
											}elseif($range == 'lastmonth'){
												$FirstDay = date('Y-m-d',strtotime("-1 month"));
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
											
											$TotalImpressions = 0;
											$TotalRevenue = 0;
											
											if($view == 2){
												$RevenuesByPT[1] = 0;
												$RevenuesByPT[2] = 0;
												$RevenuesByPT[3] = 0;
												$RevenuesByPT[4] = 0;
												$ImpressionsByPt[1] = 0;
												$ImpressionsByPt[2] = 0;
												$ImpressionsByPt[3] = 0;
												$ImpressionsByPt[4] = 0;
												
												$sql = "SELECT idTag, SUM(Impressions) AS Impressions, SUM(Revenue) AS Revenue FROM " . STATS .  " WHERE idUser = '" . $_SESSION['login'] . "' AND Date BETWEEN '$FirstDay' AND '$LastDay' GROUP BY idTag";
												$query = $db->query($sql);
												if($db->num_rows($query) > 0){
													while($dataTag = $db->fetch_array($query)){
														$Revenuevalue = $TagList[$dataTag['idTag']]['Revenue'];
														if($TagList[$dataTag['idTag']]['RevenueType'] == 1){
															$RevenueThis = $dataTag['Revenue'] * $Revenuevalue / 100;
														}else{
															$RevenueThis = $dataTag['Impressions'] * $Revenuevalue;
														}
														$RevenueThis = correctCurrency($RevenueThis, $idCurrency);
														$RevenuesByPT[$TagList[$dataTag['idTag']]['PlatformType']] += $RevenueThis;
														$ImpressionsByPt[$TagList[$dataTag['idTag']]['PlatformType']] += $dataTag['Impressions'];
													}
												}												
												//print_r($RevenuesByPT);
												for($pla = 1; $pla <= 4; $pla++){
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
													?><tr>
														<td data-title="Fecha"> <?php echo $PlaType;?></td>
														<!--<td data-title="Clicks">2,043 <i class="fa-caret-up"></i></td>
														<td data-title="Altas">124 <i class="fa-caret-down"></i></td>--><?php
													if($ShowI){
														?><td data-title="Impresiones"><?php echo number_format($ImpressionsByPt[$pla], 0, ',', '.'); ?> <!--<i class="fa-caret-up"></i>--></td>
														<!--<td data-title="% de Conversión">1.18% <i class="fa-caret-up"></i></td>-->
														<td data-title="eCPM"><?php echo $eCpmShow; ?> <!--<i class="fa-caret-up"></i>--></td><?php
													}
														?><td data-title="Ganancias"><strong><span><?php echo $DE . number_format($RevenuesByPT[$pla], 2, ',', '.') . $DEb; ?></span></strong> <!--<i class="fa-caret-down"></i>--></td>
													</tr><?php
													$TotalImpressions += $ImpressionsByPt[$pla];
													$TotalRevenue += $RevenuesByPT[$pla];
												}
												
											}elseif($view == 3 && $ShowI === true){
												
												$sql = "SELECT idTag, SUM(Impressions) AS Impressions, SUM(Revenue) AS Revenue FROM " . STATS .  " WHERE idUser = '" . $_SESSION['login'] . "' AND Date BETWEEN '$FirstDay' AND '$LastDay' GROUP BY idTag";
												$query = $db->query($sql);
												if($db->num_rows($query) > 0){
													while($dataTag = $db->fetch_array($query)){
														$sql = "SELECT TagName FROM " . TAGS . " WHERE id = '" . $dataTag['idTag'] . "' LIMIT 1";
														$TagName = $db->getOne($sql);
														
														$Revenuevalue = $TagList[$dataTag['idTag']]['Revenue'];
														if($TagList[$dataTag['idTag']]['RevenueType'] == 1){
															$RevenueThis = $dataTag['Revenue'] * $Revenuevalue / 100;
														}else{
															$RevenueThis = $dataTag['Impressions'] * $Revenuevalue;
														}
														$RevenueThis = correctCurrency($RevenueThis, $idCurrency);
														$Revenue = $RevenueThis;
														$Impressions = $dataTag['Impressions'];
																						

														if($Impressions > 0 && $Revenue > 0){
															$eCpm = $Revenue / $Impressions * 1000;
															$eCpmShow = $DE . number_format($eCpm, 2, ',', '.') . $DEb;
														}else{
															$eCpmShow = 'NA';
														}
														?><tr>
															<td data-title="Fecha"> <?php echo $TagName; ?></td>
															<!--<td data-title="Clicks">2,043 <i class="fa-caret-up"></i></td>
															<td data-title="Altas">124 <i class="fa-caret-down"></i></td>--><?php
														if($ShowI){
															?><td data-title="Impresiones"><?php echo number_format($Impressions, 0, ',', '.'); ?> <!--<i class="fa-caret-up">--></i></td>
															<!--<td data-title="% de Conversión">1.18% <i class="fa-caret-up"></i></td>-->
															<td data-title="eCPM"><?php echo $eCpmShow; ?> <!--<i class="fa-caret-up"></i>--></td><?php
														}
															?><td data-title="Ganancias"><strong><span><?php echo $DE . number_format($Revenue, 2, ',', '.') . $DEb; ?></span></strong> <!--<i class="fa-caret-down"></i>--></td>
														</tr><?php
														$TotalImpressions += $Impressions;
														$TotalRevenue += $Revenue;
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
													$sql = "SELECT idTag, SUM(Impressions) AS Impressions, SUM(Revenue) AS Revenue FROM " . STATS .  " WHERE idUser = '" . $_SESSION['login'] . "' AND Date = '$date' GROUP BY idTag";
													$query = $db->query($sql);
													$ThisImpressions = 0;
													$ThisRevenue = 0;
													if($db->num_rows($query) > 0){
														while($dataTag = $db->fetch_array($query)){
															$Revenuevalue = $TagList[$dataTag['idTag']]['Revenue'];
															//$Revenuevalue = 100;
															if($TagList[$dataTag['idTag']]['RevenueType'] == 1){
																$ThisRevenue += $dataTag['Revenue'] * $Revenuevalue / 100;
															}else{
																$ThisRevenue += $dataTag['Impressions'] * $Revenuevalue;
															}
															
															$ThisImpressions += $dataTag['Impressions'];
														}
													}

													$ThisRevenue = correctCurrency($ThisRevenue, $idCurrency);

													if($ThisImpressions > 0 && $ThisRevenue > 0){
														$eCpm = $ThisRevenue / $ThisImpressions * 1000;
														$eCpmShow = $DE . number_format($eCpm, 2, ',', '.') . $DEb;
													}else{
														$eCpmShow = 'NA';
													}
													
													$TotalImpressions += $ThisImpressions;
													$TotalRevenue += $ThisRevenue;
													
													?><tr>
														<td data-title="Fecha"> <?php echo $readDate;?></td>
														<!--<td data-title="Clicks">2,043 <i class="fa-caret-up"></i></td>
														<td data-title="Altas">124 <i class="fa-caret-down"></i></td>--><?php
													if($ShowI){
														?><td data-title="Impresiones"><?php echo number_format($ThisImpressions, 0, ',', '.'); ?> <!--<i class="fa-caret-up"></i>--></td>
														<!--<td data-title="% de Conversión">1.18% <i class="fa-caret-up"></i></td>-->
														<td data-title="eCPM"><?php echo $eCpmShow; ?> <!--<i class="fa-caret-up"></i>--></td><?php
													}
														?><td data-title="Ganancias"><strong><span><?php echo $DE . number_format($ThisRevenue, 2, ',', '.') . $DEb; ?></span></strong> <!--<i class="fa-caret-down"></i>--></td>
													</tr><?php
													$Time1 = $Time1 - 86400;
												}
											}
											
											if($TotalImpressions > 0 && $TotalRevenue > 0){
												$eCpm = $TotalRevenue / $TotalImpressions * 1000;
												$totaleCpmShow = $DE . number_format($eCpm, 2, ',', '.') . $DEb;
											}else{
												$totaleCpmShow = 'NA';
											}											
										?>
										<!--<tr>
											<td data-title="Fecha">25 de FEB 2015</td>
											<td data-title="Clicks">2,043 <i class="fa-caret-up"></i></td>
											<td data-title="Altas">124 <i class="fa-caret-down"></i></td>
											<td data-title="Impresiones">1.345.432 <i class="fa-caret-up"></i></td>
											<td data-title="% de Conversión">1.18% <i class="fa-caret-up"></i></td>
											<td data-title="eCPM">0.141€ <i class="fa-caret-up"></i></td>
											<td data-title="Ganancias" class="tx-dr"><strong><span>2,043€</span></strong> <i class="fa-caret-down"></i></td>
										</tr>
										<tr>
											<td data-title="Fecha">25 de FEB 2015</td>
											<td data-title="Clicks">2,043 <i class="fa-caret-up"></i></td>
											<td data-title="Altas">124 <i class="fa-caret-down"></i></td>
											<td data-title="Impresiones">1.345.432 <i class="fa-caret-up"></i></td>
											<td data-title="% de Conversión">1.18% <i class="fa-caret-up"></i></td>
											<td data-title="eCPM">0.141€ <i class="fa-caret-up"></i></td>
											<td data-title="Ganancias" class="tx-dr"><strong><span>2,043€</span></strong> <i class="fa-caret-down"></i></td>
										</tr>
										<tr>
											<td data-title="Fecha">25 de FEB 2015</td>
											<td data-title="Clicks">2,043 <i class="fa-caret-up"></i></td>
											<td data-title="Altas">124 <i class="fa-caret-down"></i></td>
											<td data-title="Impresiones">1.345.432 <i class="fa-caret-up"></i></td>
											<td data-title="% de Conversión">1.18% <i class="fa-caret-up"></i></td>
											<td data-title="eCPM">0.141€ <i class="fa-caret-up"></i></td>
											<td data-title="Ganancias" class="tx-dr"><strong><span>2,043€</span></strong> <i class="fa-caret-down"></i></td>
										</tr>
										<tr>
											<td data-title="Fecha">25 de FEB 2015</td>
											<td data-title="Clicks">2,043 <i class="fa-caret-up"></i></td>
											<td data-title="Altas">124 <i class="fa-caret-down"></i></td>
											<td data-title="Impresiones">1.345.432 <i class="fa-caret-up"></i></td>
											<td data-title="% de Conversión">1.18% <i class="fa-caret-up"></i></td>
											<td data-title="eCPM">0.141€ <i class="fa-caret-up"></i></td>
											<td data-title="Ganancias" class="tx-dr"><strong><span>2,043€</span></strong> <i class="fa-caret-down"></i></td>
										</tr>-->
										
										<tfoot>
										<tr>
											<th colspan="4"><span>Totales: <!--Clicks:--><?php if($ShowI){ ?>Impresiones:</span> <strong><?php echo number_format($TotalImpressions, 0, ',', '.'); ?></strong></span> <span class="txspr">·</span> <!--<span>% de Conversiones: <strong>1.19%</strong></span> <span class="txspr">·</span>--> <span>CPM: <strong><?php echo $totaleCpmShow; ?></strong></span> <span class="txspr">·</span><?php } ?> <span>Ganancias: <strong><?php echo $DE . number_format($TotalRevenue, 2, ',', '.') . $DEb; ?></strong></span></th>
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
		<!--<ftcn>-->
		<footer class="ftcn">
			<div class="ftcn-a">
				<div class="cnt">
					
					<p>Inscrita en el Registro Mercantil de Madrid, Tomo 35861, Folio 180, S 8, Hoja M-644324, CIF B-87794665<br/>Vidoomy Media S.L. C/ Silva 2, primera planta, 28008, Madrid (España). e-mail: info[at]vidoomy.com</p>
				</div>
			</div>
			<div class="ftcn-b">
				<div class="cnt">
                    <p><strong>© 2018 <span>VIDOOMY S.L.</span> <a href="#" style="color:#fff;">Política de privacidad</a></strong></p>
				</div>
			</div>
		</footer>
		<!--</ftcn>-->		
	</div>
	<!--</all>-->
    
    <!-- Javascript -->
    <script src="js/lib/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-filestyle.js"></script>
    
    <!-- Grafica -->
    <script src="js/charts/jquery.flot.min.js"></script>
    <script src="js/charts/jquery.flot.resize.min.js"></script>
    
    <script src="js/chart-2.7.1/dist/Chart.bundle.js"></script>
	<script src="js/chart-2.7.1/samples/utils.js"></script>
	<script src="js/chart-2.7.1/samples/charts/area/analyser.js"></script>

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
			$sql = "SELECT idTag, SUM(Impressions) AS Impressions, SUM(Revenue) AS Revenue FROM " . STATS .  " WHERE idUser = '" . $_SESSION['login'] . "' AND Date = '$date' GROUP BY idTag";
			$query = $db->query($sql);
			$ThisImpressions = 0;
			$ThisRevenue = 0;
			
			$GraphInfo[$showdate][1] = 0;
			$GraphInfo[$showdate][2] = 0;
			$GraphInfo[$showdate][3] = 0;
			$GraphInfo[$showdate][4] = 0;
			
			if($db->num_rows($query) > 0){
				while($dataTag = $db->fetch_array($query)){
					$Revenuevalue = $TagList[$dataTag['idTag']]['Revenue'];
					if($TagList[$dataTag['idTag']]['RevenueType'] == 1){
						$ThisRevenue = $dataTag['Revenue'] * $Revenuevalue / 100;
					}else{
						$ThisRevenue = $dataTag['Impressions'] * $Revenuevalue;
					}
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
		jQuery(document).ready(function($){
			$('#tbl-estats').dataTable({
				"paging": false,
				"lengthChange": false,
				"searching": false,
				"order": false,
				"info":     false,
                "autoWidth": false,
                "columns": [
                	null,
                    { "width": "18%" },
                    { "width": "15%" },
                    { "width": "15%" }
                ]
    		});
		});
    </script>
	
</body>
</html>