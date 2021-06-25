<?php
	session_start();
	define('CONST',1);
	
	require('../config.php');
	require('../db.php');
	require('../constantes.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	require('../common.lib.php');
	require('libs/pub-managers.lib.php');
	$pm = new PM();
	
	
	if($_SESSION['Type'] == 1 || $_SESSION['Type'] == 3){
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
	
	$sql = "SELECT * FROM " . ACC_MANAGERS . " WHERE id = '$idAccM' LIMIT 1";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		$AccM = $db->fetch_array($query);
	}else{
		header('Location: acc-managers.php');
		exit(0);
	}
	
	if(isset($_GET['p'])){
		$Period = $_GET['p'];
	}else{
		$Period = date('Y-n');
	}
	
	if($Period == date('Y-n')){
		$CurrentMonth = true;
	}else{
		$CurrentMonth = false;
	}
	
	$arP = explode('-',$Period);
	$ThisY = $arP[0];
	$ThisM = $arP[1];
	
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
    <link rel="stylesheet" href="css/css.css?=1">
    <link rel="stylesheet" href="css/thimify.css">
    <link rel="icon" type="image/png" href="img/favicon.png">
    
    
    <!-- Custom CSS -->
    <link href="assets/libs/chartist/dist/chartist.min.css" rel="stylesheet">
    <link href="assets/extra-libs/c3/c3.min.css" rel="stylesheet">
    <link href="assets/libs/morris.js/morris.css" rel="stylesheet">
    <!--[if lt IE 9]>
	    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->
	
	<style>
		
.card {
    position: relative;
    display: flex;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 1px solid #e9ecef;
    border-radius: 0;
}
.card-body {
    flex: 1 1 auto;
    padding: 1.25rem;
}
.card-group{
	margin-bottom:15px;
}
.card-body div, .card-body h2{
    color:#3e5569 !important;
}
.card-body h2, .card-body h3{
	font-weight: 400;	
}
.card.no-card-border {
    border: 0;
}
html body .bg-light {
    background-color: #e9ecef;
}
.align-items-center {
    align-items: center!important;
}
.d-flex {
    display: flex!important;
}
.m-r-10 {
    margin-right: 10px;
}
.ml-auto, .mx-auto {
    margin-left: auto!important;
}
.btn {
    display: inline-block;
    text-align: center;
    vertical-align: middle;
    user-select: none;
    border: 1px solid transparent;
    padding: .375rem .75rem;
    font-size: .875rem;
    line-height: 1.5;
    border-radius: 2px;
    transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
}
.bg-danger {
    background-color: #ef6e6e!important;
}
.btn-info {
    color: #fff;
    background-color: #4798e8;
    border-color: #4798e8;
}
.bg-success {
    background-color: #22c6ab!important;
}
.bg-warning {
    background-color: #ffbc34!important;
}
.bg-info {
    background-color: #4798e8!important;
}
.btn-circle {
    border-radius: 100%;
    width: 40px;
    height: 40px;
    padding: 10px;
}
.btn-circle.btn-lg, .btn-group-lg>.btn-circle.btn {
    width: 50px;
    height: 50px;
    padding: 14px 15px;
    font-size: 18px;
    line-height: 23px;
}
.text-white {
    color: #fff!important;
}
[class*=" ti-"], [class^=ti-] {
    font-family: themify;
    speak: none;
    font-variant: normal;
    text-transform: none;
}
.progress {
    display: flex;
    height: 5px;
    font-size: .65625rem;
    background-color: #f8f9fa;
    border-radius: 2px;
}
.progress-bar {
    display: flex;
    flex-direction: column;
    justify-content: center;
    color: #fff;
    text-align: center;
    background-color: #7460ee;
    transition: width .6s ease;
}
.bg-inverse {
    background-color: #212529;
}
.rounded-circle {
    border-radius: 50%!important;
}

	</style>
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
						<div class="titl">Objetivos de <?php echo $AccM['Name']; ?> - Vidoomy</div>
					</div>
					<div class="bx-bd">
						<div class="bx-pd">
							
							<div style="padding:1.25rem;">
							
							<a href="objetives-list.php?idam=<?php echo $idAccM; ?>">Objetivos de <?php echo $AccM['Name']; ?></a> &gt; <a href="objetives-list.php?idam=<?php echo $idAccM; ?>&y=<?php echo $ThisY; ?>"><?php echo $ThisY; ?></a> &gt; <?php echo $MonthSpanish[$ThisM]; ?>
							
							<!--<div class="cls c-fx">
								<div class="clmd04">
									<div class="frm-group d-fx lbl-lf">
										<label for="searchPage">Pagina:</label>
										<input type="text" id="searchPage" class="d-flx1">
									</div>
								</div>
								<div class="clmd04">
									<div class="frm-group d-fx lbl-lf">
										<label for="adstxtstatus">Ads.txt:</label>
										<div class="d-flx1">
											<select id="adstxtstatus">
												<option value="">Todas</option>
												<option value="correct">Correcto</option>
												<option value="inco">Con Fallos</option>
											</select>
										</div>
									</div>
								</div>

								<div class="clmd04">
									<div class="frm-group d-fx lbl-lf">
										<ul class="lst-tbs b-fx mb2" style="margin: auto; padding-top: 5px;"><?php
											if($_SESSION['Type'] == 3){
											?><li class="b-rt"><a href="adstxt.php" class="fa-plus-circle">Editar Ads.txt</a></li><?php } ?>
											<li class="b-rt"><a href="add-site.php<?php echo $link; ?>" class="fa-plus-circle">Añadir Sitio</a></li>
										</ul>
									</div>

								</div>
							</div>-->
							
							
							<div class="cls c-fx">
								<div class="clmd12">
									<div class="frm-group d-fx lbl-lf">
										<h2>Estadísticas de <?php echo $AccM['Name']; ?> mes <?php echo $MonthSpanish[$ThisM] . ' ' . $ThisY; ?></h2>
									</div>
								</div>
							</div>
							
							<?php

								$RegularDomains = $pm->getAccMDomains($idAccM, $Period, false);
								$PremiumDomains = $pm->getAccMDomains($idAccM, $Period, true);
								
								$RegularPublishers = $pm->getAccMPublishers($idAccM, $Period, false);
								$PremiumPublishers = $pm->getAccMPublishers($idAccM, $Period, true);
								
								$LostDomains = $pm->getAccMLDomains($idAccM, $Period);
								$LostPublishers = $pm->getAccMLPublishers($idAccM, $Period);
								
							?>
							
							<div class="card-group cls c-fx">
			                    <div style="width:100%;">
			                        <div class="card bg-light no-card-border">
			                            <div class="card-body">
			                                <div class="d-flex align-items-center">
			                                    <div class="m-r-10">
			                                        <img src="<?php if(file_exists('accm/' . $idAccM . '.jpg')) { echo 'accm/' . $idAccM . '.jpg'; } else { echo 'accm/foto.png'; } ?>" alt="user" width="60" class="rounded-circle" />
			                                    </div>
			                                    <div>
			                                        <h3 class="m-b-0">Objetivos de <?php echo $MonthSpanish[$ThisM] . ' ' . $ThisY; ?></h3>
			                                        <?php
				                                        if($CurrentMonth){ 
			                                        ?><span><?php echo $DaySpanish[date('w')] . ', '  . date('j') . ' de ' . $MonthSpanish[date('n')] . ' de ' . date('Y'); ?></span><?php
				                                    	}
				                                    ?>
			                                    </div>
			                                </div>
			                            </div>
			                        </div>
			                    </div>
			                </div>
							
							<?php
								$sql = "SELECT * FROM " . OBJETIVES . " WHERE AccM = '$idAccM' AND Mes = '$Period' LIMIT 1";
								$query = $db->query($sql);
								if($db->num_rows($query) > 0){
									$Obj = $db->fetch_array($query);
								}else{
									$sql = "SELECT * FROM " . OBJETIVES . " WHERE id = '1' LIMIT 1";
									$query = $db->query($sql);
									$Obj = $db->fetch_array($query);
								}
								$Revenue = $pm->getMonthRevenue($idAccM, $Period);
								$ObjRevenue = $Obj['Revenue'];
								
								$PerRevenue = number_format($Revenue * 100 / $ObjRevenue, 2, '.', ',');
								
								if($CurrentMonth){
									
									$MonthPer = date('j') * 100 / date('t');
									$ProgRevenue = $ObjRevenue * $MonthPer / 100;
									
									$PerProgRevenue = number_format($Revenue * 100 / $ProgRevenue, 2, '.', ',');
								}
								
								
								$ObjPublishers = $Obj['Publishers'];
								$ObjPublishersP = $Obj['PublishersP'];
								$ObjDominios = $Obj['Dominios'];
								$ObjDominiosP = $Obj['DominiosP'];
								
								$PubPer = $RegularPublishers * 100 / $ObjPublishers;
								$PPubPer = $PremiumPublishers * 100 / $ObjPublishersP;
								$DomPer = $RegularDomains * 100 / $ObjDominios;
								$PDomPer = $PremiumDomains * 100 / $ObjDominiosP;
							?>
							
			                <!-- ============================================================== -->
			                <!-- Sales Summery -->
			                <!-- ============================================================== -->
			                <div class="card-group cls c-fx">
			                    <!-- Column -->
			                    <div class="card clmd03">
			                        <div class="card-body">
			                            <div class="row">
			                                <div class="col-12">
			                                    <h3>
				                                    <?php /* if($RegularPublishers > 0){ ?><a href="#" id="listpubs"><?php } echo $PubPer; ?>% - <?php echo $RegularPublishers; ?> / <?php echo $ObjPublishers; ?><?php if($RegularPublishers > 0){ ?></a><?php } */ ?>
				                                	<?php echo intval($RegularPublishers); ?>
				                                </h3>
			                                    <h6 class="card-subtitle"><a href="#" id="listpubs">Nuevos Publishers</a></h6>
			                                </div>
			                                
			                                <?php if($RegularPublishers > 0){ ?>
						                    <div class="modal" id="listpub" style="display:none">
			  									<label class="modal__bg" onclick="$('#listpub').hide();"></label>
			  									<div class="modal__inner" style="width:60%">
													<label class="modal__close" onclick="$('#listpub').hide();"></label>
													<div class="bx-cn bx-shnone">
														<div class="bx-hd dfl b-fx">
															<div class="titl" style="color:#FFF !important;">Nuevos Publishers</div>
														</div>
														<div class="bx-bd">
															<!--<div class="bx-pd"><?php echo ceil($PubPer); ?>% - <?php echo $RegularPublishers; ?> / <?php echo $ObjPublishers; ?></div>-->
															<div class="bx-pd">Total Nuevos Publishers: <?php echo $RegularPublishers; ?></div>
															<table class="tbl-payments" style="margin:0">
																<thead>
																	<tr>
																		<th>Publisher</th>
																		<th>Alta</th>
																	</tr>
																</thead>
																<tbody><?php 
																	$PubList = $pm->listAccMPublishers($idAccM, $Period);
																	//print_r($PubList);	
																	if(count($PubList) > 0){
																		foreach($PubList as $Pub){
																			?><tr>
																				<td><?php echo $Pub['user']; ?></td>
																				<td><?php echo date('d.m.Y',$Pub['time']); ?></td>
																			</tr><?php
																		}
																	}
																?></tbody>
															</table>
														</div>
													</div>
		  										</div>
											</div>
											<?php } ?>
			                                
			                                <!--<div class="col-12">
			                                    <div class="progress">
			                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $PubPer; ?>%; height: 6px;" aria-valuenow="25" aria-valuemin="0"
			                                            aria-valuemax="100"></div>
			                                    </div>
			                                </div>-->
			                            </div>
			                        </div>
			                    </div>
			                    <!-- Column -->
			                    <!-- Column -->
			                    <div class="card clmd03">
			                        <div class="card-body">
			                            <div class="row">
			                                <div class="col-12">
			                                    <h3><?php /* if($PremiumPublishers > 0){ ?><a href="#" id="listpubsp"><?php } echo ceil($PPubPer); ?>% - <?php echo $PremiumPublishers; ?> / <?php echo $ObjPublishersP; ?><?php if($PremiumPublishers > 0){ ?></a><?php } */ ?>
			                                    <?php echo intval($PremiumPublishers); ?>
			                                    </h3>
			                                    <h6 class="card-subtitle"><a href="#" id="listpubsp">Nuevos publishers Premium</a></h6>
			                                </div>
			                                
			                                
			                                <?php if($PremiumPublishers > 0){ ?>
						                    <div class="modal" id="listpubp" style="display:none">
			  									<label class="modal__bg" onclick="$('#listpubp').hide();"></label>
			  									<div class="modal__inner" style="width:60%">
													<label class="modal__close" onclick="$('#listpubp').hide();"></label>
													<div class="bx-cn bx-shnone">
														<div class="bx-hd dfl b-fx">
															<div class="titl" style="color:#FFF !important;">Nuevos publishers Premium</div>
														</div>
														<div class="bx-bd">
															<!--<div class="bx-pd"><?php echo ceil($PPubPer); ?>% - <?php echo $PremiumPublishers; ?> / <?php echo $ObjPublishersP; ?></div>-->
															<div class="bx-pd">Total Nuevos publishers Premium: <?php echo $PremiumPublishers; ?></div>
															<table class="tbl-payments" style="margin:0">
																<thead>
																	<tr>
																		<th>Publisher</th>
																		<th>Alta</th>
																	</tr>
																</thead>
																<tbody><?php 
																	$PubListP = $pm->listAccMPublishers($idAccM, $Period, true);
																	//print_r($PubList);	
																	if(count($PubListP) > 0){
																		foreach($PubListP as $Pub){
																			?><tr>
																				<td><?php echo $Pub['user']; ?></td>
																				<td><?php echo date('d.m.Y',$Pub['time']); ?></td>
																			</tr><?php
																		}
																	}
																?></tbody>
															</table>
														</div>
													</div>
		  										</div>
											</div>
											<?php } ?>
			                                
			                                
			                                <!--<div class="col-12">
			                                    <div class="progress">
			                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $PPubPer; ?>%; height: 6px;" aria-valuenow="25" aria-valuemin="0"
			                                            aria-valuemax="100"></div>
			                                    </div>
			                                </div>-->
			                            </div>
			                        </div>
			                    </div>
			                    <!-- Column -->
			                    <!-- Column -->
			                    <div class="card clmd03">
			                        <div class="card-body">
			                            <div class="row">
			                                <div class="col-12">
			                                    <h3><?php /* if($RegularDomains > 0){ ?><a href="#" id="listdoms"><?php } echo $DomPer; ?>% - <?php echo $RegularDomains; ?> / <?php echo $ObjDominios; ?><?php if($RegularDomains > 0){ ?></a><?php } */ ?>
			                                    	<?php echo intval($RegularDomains); ?>
			                                    </h3>
			                                    <h6 class="card-subtitle"><a href="#" id="listdoms">Nuevos Dominios</a></h6>
			                                </div>
			                                
			                                
			                                <?php if($RegularDomains > 0){ ?>
						                    <div class="modal" id="listdom" style="display:none">
			  									<label class="modal__bg" onclick="$('#listdom').hide();"></label>
			  									<div class="modal__inner" style="width:60%">
													<label class="modal__close" onclick="$('#listdom').hide();"></label>
													<div class="bx-cn bx-shnone">
														<div class="bx-hd dfl b-fx">
															<div class="titl" style="color:#FFF !important;">Nuevos Dominios</div>
														</div>
														<div class="bx-bd">
															<!--<div class="bx-pd"><?php echo ceil($DomPer); ?>% - <?php echo $RegularDomains; ?> / <?php echo $ObjDominios; ?></div>-->
															<div class="bx-pd">Total Nuevos Dominios: <?php echo $RegularDomains; ?></div>
															<table class="tbl-payments" style="margin:0">
																<thead>
																	<tr>
																		<th>Dominio</th>
																		<th>Publisher</th>
																		<th>Alta</th>
																	</tr>
																</thead>
																<tbody><?php 
																	$DomList = $pm->listAccMDomains($idAccM, $Period);
																	//print_r($PubList);	
																	if(count($DomList) > 0){
																		foreach($DomList as $Dom){
																			?><tr>
																				<td><?php echo $Dom['siteurl']; ?></td>
																				<td><?php echo $Dom['publishername']; ?></td>
																				<td><?php if(intval($Dom['time']) != 0) { echo date('d.m.Y',$Dom['time']); } else { echo 'NA'; } ?></td>
																			</tr><?php
																		}
																	}
																?></tbody>
															</table>
														</div>
													</div>
		  										</div>
											</div>
											<?php } ?>
			                                
			                                
			                                <!--<div class="col-12">
			                                    <div class="progress">
			                                        <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $DomPer; ?>%; height: 6px;" aria-valuenow="25" aria-valuemin="0"
			                                            aria-valuemax="100"></div>
			                                    </div>
			                                </div>-->
			                            </div>
			                        </div>
			                    </div>
			                    <!-- Column -->
			                    <!-- Column -->
			                    <div class="card clmd03">
			                        <div class="card-body">
			                            <div class="row">
			                                <div class="col-12">
			                                    <h3><?php if($PremiumDomains > 0){ ?><a href="#" id="listdomsp"><?php } echo number_format($PDomPer, 2, '.', ',');; ?>% - <?php echo $PremiumDomains; ?> / <?php echo $ObjDominiosP; ?><?php if($PremiumDomains > 0){ ?></a><?php } ?></h3>
			                                    <h6 class="card-subtitle">Nuevos Dominios Premium</h6>
			                                </div>
			                                
			                                <?php if($PremiumDomains > 0){ ?>
			                                <div class="modal" id="listdomp" style="display:none">
			  									<label class="modal__bg" onclick="$('#listdomp').hide();"></label>
			  									<div class="modal__inner" style="width:60%">
													<label class="modal__close" onclick="$('#listdomp').hide();"></label>
													<div class="bx-cn bx-shnone">
														<div class="bx-hd dfl b-fx">
															<div class="titl" style="color:#FFF !important;">Nuevos Dominios Premium</div>
														</div>
														<div class="bx-bd">
															<div class="bx-pd"><?php echo number_format($PDomPer, 2, '.', ','); ?>% - <?php echo $PremiumDomains; ?> / <?php echo $ObjDominiosP; ?></div>
															<table class="tbl-payments" style="margin:0">
																<thead>
																	<tr>
																		<th>Dominio</th>
																		<th>Publisher</th>
																		<th>Alta</th>
																	</tr>
																</thead>
																<tbody><?php 
																	$DomList = $pm->listAccMDomains($idAccM, $Period, true);
																	//print_r($PubList);	
																	if(count($DomList) > 0){
																		foreach($DomList as $Dom){
																			?><tr>
																				<td><?php echo $Dom['siteurl']; ?></td>
																				<td><?php echo $Dom['publishername']; ?></td>
																				<td><?php if(intval($Dom['time']) != 0) { echo date('d.m.Y',$Dom['time']); } else { echo 'NA'; } ?></td>
																			</tr><?php
																		}
																	}
																?></tbody>
															</table>
														</div>
													</div>
		  										</div>
											</div>
			                                <?php } ?>
			                                
			                                <div class="col-12">
			                                    <div class="progress">
			                                        <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $PDomPer; ?>%; height: 6px;" aria-valuenow="25" aria-valuemin="0"
			                                            aria-valuemax="100"></div>
			                                    </div>
			                                </div>
			                            </div>
			                        </div>
			                    </div>
			                    <!-- Column -->
			                    <!-- Column -->
			                    <div class="card <?php if($CurrentMonth) { echo 'clmd06'; } else { echo 'clmd12'; } ?>">
			                        <div class="card-body">
			                            <div class="row">
			                                <div class="col-12">
			                                    <h3>
				                                    <?php /* echo ceil($PerRevenue); ?>% - $<?php echo number_format($Revenue / 1000, 1, '.', ','); ?>K / $<?php echo number_format($ObjRevenue / 1000, 1, '.', ','); ?>K */ ?>
				                                    <?php echo number_format($Revenue / 1000, 1, '.', ','); ?>K USD
					                            </h3>
			                                    <h6 class="card-subtitle">Revenue Total <?php echo $MonthSpanish[$ThisM]; ?> USD</h6>
			                                </div>
			                                <!--<div class="col-12">
			                                    <div class="progress">
			                                        <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo $PerRevenue; ?>%; height: 6px;" aria-valuenow="25" aria-valuemin="0"
			                                            aria-valuemax="100"></div>
			                                    </div>
			                                </div>-->
			                            </div>
			                        </div>
			                    </div>
			                    <!-- Column -->
			                    <?php if($CurrentMonth){ ?>
			                    <!-- Column -->
			                    <div class="card clmd06">
			                        <div class="card-body">
			                            <div class="row">
			                                <div class="col-12">
			                                    <h3><?php echo ceil($PerProgRevenue); ?>% - $<?php echo number_format($Revenue / 1000, 1, '.', ','); ?>K / $<?php echo number_format($ProgRevenue / 1000, 1, '.', ','); ?>K</h3>
			                                    <h6 class="card-subtitle">Comparación Revenue Mes Anterior</h6>
			                                </div>
			                                <div class="col-12">
			                                    <div class="progress">
			                                        <div class="progress-bar bg-inverse" role="progressbar" style="width: <?php echo $PerProgRevenue; ?>%; height: 6px;" aria-valuenow="25" aria-valuemin="0"
			                                            aria-valuemax="100"></div>
			                                    </div>
			                                </div>
			                            </div>
			                        </div>
			                    </div>
			                    <?php } ?>
			                </div>
							<?php
								/* }else{
							?>	
							<div class="card-group cls c-fx">
			                    <!-- Column -->
			                    <div class="card clmd12">
			                        <div class="card-body">
			                            <div class="row">
			                                <div class="col-12">
			                                    <h6 class="card-subtitle" style="text-align: center; padding-top:10px;">No hay Objetivos establecidos para este periodo.</h6>
			                                </div>
			                                
			                            </div>
			                        </div>
			                    </div>
			                </div>
							<?php
								}
							?>
							*/ ?>
							
							<div class="cls c-fx">
								<div class="clmd12">
									<div class="frm-group d-fx lbl-lf">
										<h2>Global Acumulado de <?php echo $AccM['Name']; ?></h2>
									</div>
								</div>
							</div>
							
							
							<div class="card-group cls c-fx">
			                    <!-- Card -->
			                    <div class="card clmd03" >
			                        <div class="card-body">
			                            <div class="d-flex align-items-center">
			                                <div class="m-r-10">
			                                    <span class="btn btn-circle btn-lg bg-success">
			                                        <i class="ti-face-smile text-white"></i>
			                                    </span>
			                                </div>
			                                <div>
			                                    Publishers
			                                </div>
			                                <div class="ml-auto">
			                                    <h2 class="m-b-0 font-light"><?php echo $pm->getAccMPublishers($idAccM, false, false); ?></h2>
			                                </div>
			                            </div>
			                        </div>
			                    </div>
			                    <!-- Card -->
			                    <!-- Card -->
			                    <div class="card clmd03">
			                        <div class="card-body">
			                            <div class="d-flex align-items-center">
			                                <div class="m-r-10">
			                                    <span class="btn btn-circle btn-lg btn-info">
			                                        <i class="ti-link text-white"></i>
			                                    </span>
			                                </div>
			                                <div>
			                                    Dominios
			                                </div>
			                                <div class="ml-auto">
			                                    <h2 class="m-b-0 font-light"><?php echo $pm->getAccMDomains($idAccM, false, false); ?></h2>
			                                </div>
			                            </div>
			                        </div>
			                    </div>
			                    <!-- Card -->
			                    <!-- Card -->
			                    <div class="card clmd03">
			                        <div class="card-body">
			                            <div class="d-flex align-items-center">
			                                <div class="m-r-10">
			                                    <span class="btn btn-circle btn-lg bg-danger">
			                                        <i class="ti-face-smile text-white"></i>
			                                    </span>
			                                </div>
			                                <div>
			                                    Publishers Premium
			                                </div>
			                                <div class="ml-auto">
			                                    <h2 class="m-b-0 font-light"><?php echo $pm->getAccMPublishers($idAccM, false, true); ?></h2>
			                                </div>
			                            </div>
			                        </div>
			                    </div>
			                    <!-- Card -->
			                    <!-- Card -->
			                    <div class="card clmd03">
			                        <div class="card-body">
			                            <div class="d-flex align-items-center">
			                                <div class="m-r-10">
			                                    <span class="btn btn-circle btn-lg bg-warning">
			                                        <i class="ti-link text-white"></i>
			                                    </span>
			                                </div>
			                                <div>
			                                    Dominios Premium
			                                </div>
			                                <div class="ml-auto">
			                                    <h2 class="m-b-0 font-light"><?php echo $pm->getAccMDomains($idAccM, false, true); ?></h2>
			                                </div>
			                            </div>
			                        </div>
			                    </div>
			                    <!-- Card -->
			                    <!-- Column -->
							</div>
							
							
							</div>
							<?php
								
								$TopDomains = $pm->getTopDomainsRevenue($idAccM, $Period, 4);
								//print_r($TopDomains);
								if(is_array($TopDomains)){
									if(count($TopDomains) > 0){
										$arP = explode('-', $Period);
										if($arP[1] == 1){
											$LastPeriod = $arP[0] - 1 . '-12';
										}else{
											$NM = $arP[1] - 1;
											$LastPeriod = $arP[0] . '-' . $NM;
										}
										//echo $LastPeriod;
							?>
							<!-- ============================================================== -->
			                <!-- Top Selliing Products -->
			                <!-- ============================================================== -->
			                <div class="row">
			                    <div class="clmd1212">
			                        <div class="card">
			                            <div class="card-body">
			                                <!-- title -->
			                                <div class="d-md-flex align-items-center">
			                                    <div>
			                                        <h4 class="card-title">Top Dominios</h4>
			                                    </div>
			                                    
			                                </div>
			                                <!-- title -->
			                            </div>
			                            <div class="table-responsive">
			                                <table class="table v-middle">
			                                    <thead>
			                                        <tr class="bg-light">
			                                            <th class="border-top-0">Dominio</th>
			                                            <th class="border-top-0">Publisher</th>
			                                            <th class="border-top-0">% Mes Pasado</th>
			                                            <th class="border-top-0">Revenue este Mes</th>
			                                        </tr>
			                                    </thead>
			                                    <tbody><?php
				                                    foreach($TopDomains as $idSite => $Revenue){
					                                    $sql = "SELECT * FROM " . SITES . " WHERE id = '$idSite' LIMIT 1";
					                                    $query = $db->query($sql);
					                                    $Site = $db->fetch_array($query);
					                                    
					                                    $sql = "SELECT nick FROM " . USERS . " WHERE id = '" . $Site['idUser'] . "' LIMIT 1";
					                                    $Publisher = $db->getOne($sql);
					                                    if($Publisher == ''){
						                                    $sql = "SELECT user FROM " . USERS . " WHERE id = '" . $Site['idUser'] . "' LIMIT 1";
						                                    $Publisher = $db->getOne($sql);
						                                }
						                                
						                                $LastRevenue = $pm->getDomainsRevenue($idSite, $LastPeriod, $CurrentMonth);
						                                
						                                if($LastRevenue > 0){
							                                if($LastRevenue >= $Revenue){
								                                $Sign = '-';
								                                $Dif = $LastRevenue - $Revenue;
								                                $varPer = $Dif * 100 / $LastRevenue;
								                                $colFormat = ' style="color:red;"';
							                                }else{
								                                $Sign = '+';
								                                $Dif = $Revenue - $LastRevenue;
								                                $varPer = $Dif * 100 / $LastRevenue;
								                                $colFormat = ' style="color:green;"';
							                                }
							                            }else{
								                            $Sign = '';
								                            $colFormat = '';
							                            }
						                                
						                                $Avr = substr($Site['siteurl'], 0, 2);
						                                if($Avr == 'ww' OR $Avr == 'ht'){
							                                $arA = explode('.', $Site['siteurl']);
							                                $Avr = substr($arA[1], 0, 2);
						                                }
						                                
			                                    	?>
			                                        <tr>
			                                            <td>
			                                                <div class="d-flex align-items-center">
			                                                    <div class="m-r-10">
			                                                        <a class="btn btn-circle btn-info text-white"><?php echo $Avr; ?></a>
			                                                    </div>
			                                                    <div class="">
			                                                        <h4 class="m-b-0 font-16"><?php echo $Site['siteurl']; ?></h4>
			                                                    </div>
			                                                </div>
			                                            </td>
			                                            <td><?php echo $Publisher; ?></td>
			                                            <td<?php echo $colFormat; ?>><?php if($Sign != '' ) { echo $Sign . number_format($varPer, 2, '.', ',') . '%'; } else { echo 'NA'; } ?></td>
			                                            <td>
			                                                <h5 class="m-b-0">$<?php echo number_format($Revenue / 1000, 1, '.', ','); ?>K</h5>
			                                            </td>
			                                        </tr>
			                                        <?php
				                                    }
			                                        ?>
			                                    </tbody>
			                                </table>
			                            </div>
			                        </div>
			                    </div>
			                </div>
			                <!-- ============================================================== -->
			                <!-- Top Selliing Products -->
			                <!-- ============================================================== -->
							<?php
								
								}
							}
								
							?>
							
							
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
    
	<!-- apps -->
    <script src="dist/js/app.min.js"></script>
    <script src="dist/js/app.init.js"></script>
    <script src="dist/js/app-style-switcher.js"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
    <script src="assets/extra-libs/sparkline/sparkline.js"></script>
    <!--Wave Effects -->
    <script src="dist/js/waves.js"></script>
    <!--Menu sidebar -->
    <script src="dist/js/sidebarmenu.js"></script>
    <!--Custom JavaScript -->
    <script src="dist/js/custom.min.js"></script>
    <!--This page JavaScript -->
    <!--chartis chart-->
    <script src="assets/libs/chartist/dist/chartist.min.js"></script>
    <script src="assets/libs/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js"></script>
    <!--c3 charts -->
    <script src="assets/extra-libs/c3/d3.min.js"></script>
    <script src="assets/extra-libs/c3/c3.min.js"></script>
    <!--chartjs -->
    <script src="assets/libs/raphael/raphael.min.js"></script>
    
	<script>
		$('#listpubs').click(function(e){
			e.preventDefault();
			$('#listpub').show();
		});
		$('#listpubsp').click(function(e){
			e.preventDefault();
			$('#listpubp').show();
		});
		$('#listdoms').click(function(e){
			e.preventDefault();
			$('#listdom').show();
		});
		$('#listdomsp').click(function(e){
			e.preventDefault();
			$('#listdomp').show();
		});
	</script>
	
</body>
</html>