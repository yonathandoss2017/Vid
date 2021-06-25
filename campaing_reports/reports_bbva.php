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
	
	$JsonData = file_get_contents('http://pix.vidoomy.com/bbva/events.php');
	
	if($_SESSION['Admin']!=1){
		header('Location: https://login.vidoomy.com/admin/login.php');
		exit(0);
	}
	
	$active1 = ' class="active"';
	$active5 = '';
	$repDay = false;
	$repEvents = false;
	$sesEvents = false;
	$Sites = false;
	
	/*
	if(isset($_GET['d'])){
		$active1 = '';
		$active2 = ' class="active"';
		
		$repDay = true;
	}elseif(isset($_GET['e'])){
		$active1 = '';
		$active3 = ' class="active"';
		
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
	*/
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
				<span>Declarando</span>
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
								<span>Events</span>
							</a>
						</li>
						<!--<li<?php //echo $active5; ?>>
							<a href="reports.php?s=1">
								<span class="icon dashboard"></span>
								<span>White List</span>
							</a>
						</li>-->
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
								
									
									$EventsData = json_decode($JsonData);
									
									$EventsList['loaded'] = 'Loaded';
									$EventsList['screen'] = 'On Screen';
									$EventsList['landing'] = 'Landing';
									$EventsList['click'] = 'Conocer más';
									$EventsList['social1'] = 'Facebook';
									$EventsList['social2'] = 'Twitter';
									$EventsList['social3'] = 'Instagram';
									$EventsList['social4'] = 'Youtube';
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
								
									?>
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