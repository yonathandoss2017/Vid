<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 1);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('../config.php');
	require('../constantes.php');
	require('../db.php');
	require('../common.lib.php');
	require('libs/common.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	date_default_timezone_set('America/New_York');
		
	$active1 = ' class="active"';
	$active2 = '';
	$active3 = '';
	$active4 = '';
	$active5 = '';
	$repDay = false;
	$repEvents = false;
	$sesEvents = false;
	$Sites = false;
	
	if(isset($_GET['d'])){
		$active1 = '';
		$active2 = ' class="active"';
		
		$repDay = true;
	}elseif(isset($_GET['e'])){
		if(intval($_GET['e']) == 2){
			$active4 = ' class="active"';
		}else{
			$active3 = ' class="active"';
		}
		$active1 = '';
		
		$repEvents = true;
	}elseif(isset($_GET['se'])){
		$active1 = '';
		$active4 = ' class="active"';
		
		$sesEvents = true;
	}elseif(isset($_GET['s'])){
		$active1 = '';
		$active5 = ' class="active"';
		
		$Sites = true;
	}
?><!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title></title>
<meta name="description" content="">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,700" rel="stylesheet">
<link rel="stylesheet" href="https://use.typekit.net/bxo2shj.css">
<link rel="stylesheet" href="js/assets/daterangepicker/daterangepicker.css">
<link rel="stylesheet" href="css/main.css">
<script>(function(h){h.className=h.className.replace(/\bno-js\b/,'')})(document.documentElement);</script>
<!--[if lt IE 9]>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
<![endif]-->
</head>
<body class="reports">
	<div class="container">
		<header>
			<div class="row-logo">
				<div class="logo1">
					<a href="javascript:void(0)">
						<img src="img/logo.png" alt="vidoomy logo">
					</a>
				</div>
				<button type="button" class="burger">
					<span class="line"></span>
					<span class="line"></span>
					<span class="line"></span>
				</button>
			</div>
			<div class="breadcrumb">
				<a href="/">Reportes</a>
				<span>Sesame</span>
			</div>
			<div class="notif-bell"></div>
			<div class="user dropdown">
				<div class="user-img"></div>
				<span>vidoomy</span>
				<ul class="dropdown-menu">
					<li>
						<a href="javascript:void(0)">Cerrar sesión</a>
					</li>
				</ul>
			</div>
		</header>

		<nav class="navbar">
			<ul class="menu">
				<li class="has-sub active">
					<a href="javascript:void(0)">
						<span class="icon reports"></span>
						<span>Reportes</span>
					</a>
					<ul class="submenu">
						<li<?php echo $active1; ?>>
							<a href="reports.php">
								<span class="icon dashboard"></span>
								<span>Cookies Users</span>
							</a>
						</li>
						<li<?php echo $active2; ?>>
							<a href="reports.php?d=<?php echo date('Y-m-d'); ?>">
								<span class="icon dashboard"></span>
								<span>Cookies Day</span>
							</a>
						</li>
						<li<?php echo $active3; ?>>
							<a href="reports.php?e=1">
								<span class="icon dashboard"></span>
								<span>Events Campaing 1</span>
							</a>
						</li>
						<li<?php echo $active4; ?>>
							<a href="reports.php?e=2">
								<span class="icon dashboard"></span>
								<span>Events Campaing 2</span>
							</a>
						</li>
						<li<?php echo $active5; ?>>
							<a href="reports.php?s=1">
								<span class="icon dashboard"></span>
								<span>White List</span>
							</a>
						</li>
					</ul>
				</li>
			</ul>
		</nav>

		<section class="main">
	      

		  <div class="panel panel-table reports-panel">
			  
			  <div class="panel-body">
				  <div class="table-container" id="table-container">
					  
				  </div>
				  
				  <div class="results">
						<div class="results-showing" style="width: 100%; overflow: auto;">
							<table id="report-table" class=" panel-table" style="width:100%"><?php
								if($repDay){
									?>
									<thead>
										<tr>
											<th>Site</th>
											<th>Cookies Detectadas</th>
											<th>Cookies Únicas Detectadas</th>
										</tr>
									</thead>
									<tbody><?php
										$D = $_GET['d'];
										
										$sql = "SELECT DISTINCT(idSite) FROM `sesamecookies` WHERE Date = '$D' AND idSite > 0 ORDER BY idSite ASC";
										$query = $db->query($sql);
										if($db->num_rows($query) > 0){
											while($Site = $db->fetch_array($query)){
												$idSite = $Site['idSite'];
												
												$sql = "SELECT siteurl FROM sites WHERE id = '$idSite' LIMIT 1";
												$siteURL = $db->getOne($sql);
												
												$sql = "SELECT COUNT(id) FROM sesamecookies WHERE idSite = '$idSite' AND Date = '$D'";
												$Event1 = $db->getOne($sql);
												
												$sql = "SELECT COUNT(DISTINCT(IP)) FROM sesamecookies WHERE idSite = '$idSite' AND Date = '$D'";
												$Event1U = $db->getOne($sql);

												?><tr>
													<td><?php echo $siteURL; ?></td>
													<td><?php echo $Event1; ?></td>
													<td><?php echo $Event1U; ?></td>
												</tr><?php
											}
										}
									?>
									</tbody><?php
								}elseif($repEvents){
									if($_GET['e'] == 2){
										$Camp = 'Vidoomytest2';
									}else{
										$Camp = 'Vidoomytest1';
									}
									$JsonData = file_get_contents('http://pix.vidoomy.com/sesame/events.php?c=' . $Camp);
									$EventsData = json_decode($JsonData);
									
									$EventsList['conversion'] = 'Conversion';
									$EventsList['loaded'] = 'Loaded';
									$EventsList['screen'] = 'Screen';
									$EventsList['landing'] = 'Landing';
									$EventsList['close'] = 'Close';
									$EventsList['click1'] = 'Crear cuenta';
									$EventsList['click2'] = 'Pruebalo';
									$EventsList['click3'] = 'Ventajas';
									$EventsList['click4'] = 'Pregunta';
									$EventsList['sendform'] = 'Form Sent';
									$EventsList['formerror'] = 'Form Fail';
									
									?>
									<thead>
										<tr>
											<th>Date</th><?php
												foreach($EventsList as $EventName){
													?><th><?php echo $EventName; ?></th><?php
												}
										?>
										</tr>
									</thead>
									<tbody><?php
            						foreach($EventsData as $Date => $Event){
	            						?><tr><td><?php echo $Date; ?></td><?php
		            					foreach($EventsList as $KEvent => $EventName){
			            					?><td><?php
				            					if(isset($Event->$KEvent)){
				            						echo $Event->$KEvent;
				            					} else {
					            					echo 0;
				            					}
				            					?></td><?php
		            					}
	            						?></tr><?php
            						}
									?></tbody><?php
								}elseif($sesEvents){
									
									$JsonData = file_get_contents('http://pix.vidoomy.com/sesame/sevents.php');
									$EventsData = json_decode($JsonData);
																		
									$EventsList['event1'] = 'Phone Click';
									$EventsList['event2'] = 'Chat Click';
									$EventsList['event3'] = 'New Account';
									$EventsList['event4'] = 'Form Sent';
									
									?>
									<thead>
										<tr>
											<th>Date</th><?php
												foreach($EventsList as $EventName){
													?><th><?php echo $EventName; ?></th><?php
												}
										?>
										</tr>
									</thead>
									<tbody><?php
            						foreach($EventsData as $Date => $Event){
	            						?><tr><td><?php echo $Date; ?></td><?php
		            					foreach($EventsList as $KEvent => $EventName){
			            					?><td><?php
				            					if(isset($Event->$KEvent)){
				            						echo $Event->$KEvent;
				            					} else {
					            					echo 0;
				            					}
				            					?></td><?php
		            					}
	            						?></tr><?php
            						}
									?></tbody><?php
									
								}elseif($Sites){
									
									?>
									<thead>
										<tr>
											<!--<th>Site Name</th>-->
											<th>Site URL</th>
											<th>Tag</th>
											<th>DIV ID</th>
											<th>Active</th>
										</tr>
									</thead>
									<tbody><?php
	            						$sql = "SELECT 
	            							s.id, s.sitename, s.siteurl, s.filename, a.divID 
	            							FROM sites s 
	            							INNER JOIN ads a ON a.idSite = s.id 
	            							WHERE 
	            								(a.Type = 1 OR a.Type = 2) 
	            							GROUP BY s.sitename";
										$query = $db->query($sql);
										if($db->num_rows($query) > 0){
											while($Site = $db->fetch_array($query)){
												$idSite = $Site['id'];
												$Status = '';
												$Action = 'enabledisable';
												$Text = 'Activo';
												
												$sql = "SELECT COUNT(*) FROM interactivecampaings WHERE idSite = '$idSite' AND idCampaing = 1 LIMIT 1";
					            				if($db->getOne($sql) == 0){ $Status = ' pending'; $Text = 'Inactivo'; }
												
			            						?><tr>
				            						<!--<td><?php echo $Site['sitename']; ?></td>-->
				            						<td><?php echo $Site['siteurl']; ?></td>
				            						<td><a href="<?php echo $Site['filename']; ?>" target="_blank"><?php echo $Site['filename']; ?></a></td>
				            						<td><?php echo $Site['divID']; ?> <a href="" class="icon-small edit popup-trigger btn-new-user" style="height:15px;"></a></td>
				            						<td><a href="control.php?d=<?php echo $Site['id']; ?>" class="<?php echo $Action; ?>"><span class="payment-icon<?php echo $Status; ?>"></span><span class="m-hidden"><?php echo $Text; ?></span></a></td><?php
			            						?></tr><?php
											}
										}
									?></tbody><?php
									
								}else{
									?>
									<thead>
										<tr>
											<th>Date</th>
											<th>Cookies Enviadas</th>
											<th>Cookies Enviadas Únicas</th>
											<th>Cookies de Regreso</th>
											<th>Cookies de Regreso Únicas</th>
											<th>Porcentaje de Retorno</th>
										</tr>
									</thead>
									<tbody><?php
										$sql = "SELECT DISTINCT(Date) FROM `sesamecookies` ORDER BY Date DESC";
										$query = $db->query($sql);
										if($db->num_rows($query) > 0){
											while($Date = $db->fetch_array($query)){
												$D = $Date['Date'];
												$sql = "SELECT COUNT(id) FROM sesamecookies WHERE Event = 1 AND Date = '$D'";
												$Event1 = $db->getOne($sql);
												
												$sql = "SELECT COUNT(DISTINCT(IP)) FROM sesamecookies WHERE Event = 1 AND Date = '$D'";
												$Event1U = $db->getOne($sql);
												
												$sql = "SELECT COUNT(id) FROM sesamecookies WHERE Event = 2 AND Date = '$D'";
												$Event2 = $db->getOne($sql);
												
												$sql = "SELECT COUNT(DISTINCT(IP)) FROM sesamecookies WHERE Event = 2 AND Date = '$D'";
												$Event2U = $db->getOne($sql);
												
												$Per = $Event2 / $Event1 * 100;
												$Per = number_format($Per, 2, ',', '');
												?><tr>
													<td><a href="reports.php?d=<?php echo $D; ?>"><?php echo $D; ?></a></td>
													<td><?php echo $Event1; ?></td>
													<td><?php echo $Event1U; ?></td>
													<td><?php echo $Event2; ?></td>
													<td><?php echo $Event2U; ?></td>
													<td><?php echo $Per; ?>%</td>	
												</tr><?php
											}
										}
									?>
									</tbody><?php
										}
									?>
								<tfoot>
									
								</tfoot>
							</table>
						</div>
						<div class="results-pagination">
							
						</div>
					</div> 
				  
				  
				  
				  
				  
	

				  
				  
				  
				  
			  </div>
		  </div>
		  
		  
		  
	<div class="popup-wrapper">
		<div class="popup-container popup-edit">
          <div class="popup-header">
            <h4><span class="icon"></span>Editar DIV ID</h4>
            <a href="javascript:void(0)" class="close-btn"></a>
          </div>
          <div class="popup-body">
            <div class="info">
              <h3 id="popwebname">A - WEB name</h3>
              <a href="" target="_blank" id="popweburl">www.webname.com</a>
            </div>
            <hr>
            <form>
			  <p class="check-message"></p>
              <div class="form-field">
                <label for="divid"><span class="required">*</span>DIV ID/CLASS</label>
                <input type="text" id="divid">
              </div>
              <div class="form-field">
                <label for="line">Línea</label>
                <input type="text" id="line" style="width:50px;">
              </div>
              <div class="form-field">
                <label for="periodicType"><span class="required">*</span>TIPO DE CONTENEDOR</label>
                <div class="field-container">
                  <div class="radio-field">
                    <input type="radio" name="type" value="periodic" id="periodicType" checked="checked">
                    <label for="periodicType">ID</label>
                  </div>
                  <div class="radio-field">
                    <input type="radio" name="type" value="onlineTv" id="onlineTvType">
                    <label for="periodicType">Class</label>
                  </div>
                </div>
              </div>
              
				<hr>
				<div class="form-field">
					<div class="align-right">
						<a href="javascript:void(0)" class="btn btn-square close-btn">Cancelar</a>
						<a href="javascript:void(0)" class="btn btn-square flat-btn form-submit">Guardar cambios</a>
					</div>
				</div>
            </form>
          </div>
        </div>
    </div>       
        
        
        
        
		
		</section>

		<footer>
			<div class="address m-hidden">
				<span>Vidoomy Media S.L.</span> – ESB87794665 – C/ Quintana 2, 2ª Planta Madrid, España (28008) - info@vidoomy.com
			</div>
			<div class="copyright">
				© 2019 Vidoomy Media S.L.  |  <a href="#"><span>Política de privacidad</span></a>
			</div>
		</footer>
	</div>

<script src="js/assets/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="js/assets/daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="js/assets/daterangepicker/daterangepicker.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.bundle.min.js"></script>

<script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="js/main.js"></script>
<script src="js/jquery-dynamicNumber.js"></script>
<script>
	$('.enabledisable').click(function(e){		
		e.preventDefault();
		var link = $(this);
		var url = $(this).attr('href');
		var jqxhr = $.get(url , function(a) {
		  if(a == 1){
			  link.children('span.payment-icon').removeClass('pending');
			  link.children('span.m-hidden').html('Active');
		  }else{
			  link.children('span.payment-icon').addClass('pending');
			  link.children('span.m-hidden').html('Inactivo');
		  }
		});
	});

						
	$('#report-table').DataTable( {
		"order": [[ 0, "asc" ]],
        "searching": false,
        "bLengthChange" : false,
        "paging":   false,
        "bInfo" : false
    });

function number_format (number, decimals, dec_point, thousands_sep) {
    // Strip all characters but numerical ones.
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}


</script>
</body>
</html>