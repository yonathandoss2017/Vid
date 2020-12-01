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
	
	$Date = date('Y-m-d');
	$Data = getLiveData($Date);
	
	if($_SESSION['Admin']!=1){
		header('Location: https://login.vidoomy.com/admin/login.php');
		exit(0);
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
	<!-- <div class="design design3"></div> -->
	<div class="action-modals-wrapper">
		<div class="action-modals success">
			<div class="icon"></div>
			<p>Tus cambios se han realizado correctamente</p>
		</div>
		<div class="action-modals error">
			<div class="icon"></div>
			<p>Ops, los cambios no se han guardado correctamente</p>
		</div>
	</div>
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
				<span>Dashboard</span>
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
						<li class="active">
							<a href="reports.html">
								<span class="icon dashboard"></span>
								<span>Dashboard</span>
							</a>
						</li>
					</ul>
				</li>
			</ul>
		</nav>

		<section class="main">
	      <h1 class="page-title">Today live</h1>
	      <p class="page-subtitle">Data overview of your publisher account</p>

		  <div class="row-featured">
			  <div class="item red">
				  <div class="img-wrapper">
					  <img src="img/feat-red.png" alt="">
				  </div>
				  <h2 class="dynamicNumberMoney" id="dynRev" data-from="<?php echo $Data['Revenue']; ?>">$<?php echo number_format($Data['Revenue'], 2, '.', ','); ?></h2>
				  <p class="subtitle">Revenue</p>
			  </div>
			  <div class="item purple2">
				  <div class="img-wrapper">
					  <img src="img/feat-purple2.png" alt="">
				  </div>
				  <h2 class="dynamicNumberMoney" id="dynPro" data-from="<?php echo $Data['Profit']; ?>">$<?php echo number_format($Data['Profit'], 2, '.', ','); ?></h2>
				  <p class="subtitle">Profit</p>
			  </div>
			  <div class="item purple">
				  <div class="img-wrapper">
					  <img src="img/feat-purple.png" alt="">
				  </div>
				  <h2 class="dynamicNumber" id="dynFlo" data-from="<?php echo $Data['formatLoads']; ?>"><?php echo number_format($Data['formatLoads'], 0, '.', ','); ?></h2>
				  <p class="subtitle">Format Loads</p>
			  </div>
			  <div class="item green">
				  <div class="img-wrapper">
					  <img src="img/feat-green.png" alt="">
				  </div>
				  <h2 class="dynamicNumber" id="dynImp" data-from="<?php echo $Data['Impressions']; ?>"><?php echo number_format($Data['Impressions'], 0, '.', ','); ?></h2>
				  <p class="subtitle">Impresiones</p>
			  </div>
			  <div class="item orange">
				  <div class="img-wrapper">
					  <img src="img/feat-orange.png" alt="">
				  </div>
				  <h2 class="dynamicNumberPor" id="dynFlf" data-from="<?php echo $Data['formatLoadFill']; ?>"><?php echo $Data['formatLoadFill']; ?>%</h2>
				  <p class="subtitle">Format Loads Fillrate</p>
			  </div>
		  </div>

		  <div class="panel panel-table reports-panel">
			  <div class="panel-header">
					<div class="left">
						<div class="dropdown form th-s-panel dark">
							<button type="button" name="report-type" id="report-type">Overall Report</button>
							<ul class="dropdown-menu">
								<li><a href="#" data-value="1">Overall Report</a></li>
								<li><a href="#" data-value="2">Monthly Report</a></li>
								<li><a href="#" data-value="3">Daily Report</a></li>
								<li><a href="#" data-value="4">Hourly Report</a></li>
							</ul>
						</div>
						<div class="date-wrapper reports">
							<div id="reportrange">
								<span class="date">Date</span>
								<span class="from">From <span></span></span>
								<span class="to">To <span></span></span>
								<span class="caret"></span>
							</div>
						</div>
						<div class="slide-panel form th-s-panel" id="Dimensions">
							<button type="button" class="inner-text">Dimension  <span class="text"></span></button>
							<ul class="slide-menu checkboxes">
								<li class="block">
									<input id="select-all-dimensions" class="select-all" type="checkbox" value="">
									<label for="select-all-dimensions">Select all</label>
								</li><?php
									foreach($DimensionsSQL as $DimK => $DimV){
										?><li>
											<input id="<?php echo $DimV['OrderVal']; ?>" type="checkbox" value="<?php echo $DimK; ?>" class="inputC dimension"<?php if($DimV['Checked'] == true) { echo ' checked="checked"'; }; ?> />
											<label for="<?php echo $DimV['OrderVal']; ?>">
												<span class="icon"></span>
												<span title="<?php echo $DimV['OrderVal']; ?>"><?php echo $DimK; ?></span>
											</label>
										</li><?php
									}
								?>
							</ul>
						</div>
						<div class="slide-panel form th-s-panel">
							<button type="button" class="inner-text">Metric  <span class="text"></span></button>
							<ul class="slide-menu checkboxes">
								<li class="block">
									<input id="select-all-metrics" class="select-all" type="checkbox" value="">
									<label for="select-all-metrics">Select all</label>
								</li><?php
									foreach($MetricsSQL as $MetK => $MetV){
								?><li>
									<input id="<?php echo $MetV['Name']; ?>" type="checkbox" value="<?php echo $MetK; ?>" class="inputC metric"<?php if($MetV['Checked'] == true) { echo ' checked="checked"'; }; ?>>
									<label for="<?php echo $MetV['Name']; ?>">
										<span class="icon"></span>
										<span title="<?php echo $MetV['Name']; ?>"><?php echo $MetK; ?></span>
									</label>
								</li><?php
									}
								?>
							</ul>
						</div>
						<div class="filter-dropdown">
							<a href="javascript:void(0)" class="fd-btn">Filters</a>
							<div class="fd-menu">
								<ul class="fdm-body">
									<li class="template-filter filter-wrapper">
										<div class="left">
											<p class="filter-name"></p>
										</div>
										<div class="right">
											<div class="form-field check-option">
												<input type="checkbox" name="fc-" id="fc-" checked="checked">
												<label for="fc-"><span class="text">show</span></label>
											</div>
											<div class="flex-top">
												<div class="dropdown form th-s-panel">
													<button type="button" name="payment-method" class="include">Include</button>
													<ul class="dropdown-menu">
														<li><a href="#" data-value="include">Include</a></li>
														<li><a href="#" data-value="exclude">Exclude</a></li>
													</ul>
												</div>
												<div class="rm-box-wrapper">

												</div>
											</div>
											<div class="form-field">
												<div class="search-input">
													<input type="text"placeholder="Search">
													<a href="javascript:void(0)" class="clear-search"></a>
												</div>
												<a href="javascript:void(0)" class="rm-btn"></a>
												<div class="search-res-wrapper">
													
												</div>
								            </div>
										</div>
									</li>
									
								</ul>
								<div class="fdm-footer">
									<span>Add</span>
									<a href="javascript:void(0)" class="new-filter">Supply Source</a>
									<a href="javascript:void(0)" class="new-filter">Supply Partner</a>
									<a href="javascript:void(0)" class="new-filter">Country</a>
									<span class="more-filters">
										<span class="main-mf">Other</span>
										<span class="mf-inner">
											<!--<a href="javascript:void(0)" class="new-filter other">Demand deal</a>-->
											<a href="javascript:void(0)" class="new-filter other">Domain</a>
										</span>
									</span>
								</div>
							</div>
						</div>
					</div>
					<div class="right">
						<button class="btn btn-square">Clear selection</button>
						<button class="btn btn-square btn-report" id="run-report"><span class="icon"></span>Run report</button>
					</div>
			  	</div>
			  	<div class="panel-body" style="min-height: 700px;">
					<div class="table-container" id="table-container">
					  
					</div>
				
					<div class="results">
						<div class="results-showing">
							
						</div>
						<div class="results-pagination">
							
						</div>
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
<!-- <script src="js/assets/Chart.bundle.min.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.bundle.min.js"></script>
<!-- <script src="js/assets/stupid-table/stupidtable.min.js"></script> -->
<script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="js/main.js"></script>
<script src="js/jquery-dynamicNumber.js"></script>
<script>
	DExec = false;
	//REPORT SENDING
	$('#run-report').click(function(){
		var fVal = '';
		var Dimensions = [];
		var Metrics = [];
		var dateVal = [];
		var repData = [];
		var Filters = [];
		if($('.dimension').length > 0){
	    	$('.dimension').each(function(i, obj) {
				if($(this).prop('checked') == true){
					Dimensions.push($(this).val());
				}
			});
    	}
    	
    	if($('.metric').length > 0){
	    	$('.metric').each(function(i, obj) {
				if($(this).prop('checked') == true){
					Metrics.push($(this).val());
				}
			});
    	}
    	
    	//console.dir(Dimensions);
    	//console.dir(Metrics);
    	
    	if($('.filter-wrapper').length > 0){
	    	$('.filter-wrapper').each(function(i, obj) {
				fVal = $(this).find('.filter-name').html();
				
				if($(this).find('.check-option input').prop('checked') == true){
					$(this).find('.rm-box').each(function() {
						var incexc = '';
						if($(this).hasClass('exclude')){
							incexc = 'exclude';
						}else{
							incexc = 'include';
						}
						var getH = $(this).clone();
						getH.find('span').remove();
						Filters.push({label: fVal, value: getH.html(), include: incexc});
					});
				}
			});
    	}
    	
    	console.dir(Filters);
    	
    	
    	dateVal.push($('#reportrange .from span').html());
    	dateVal.push($('#reportrange .to span').html());
    	
    	repData['Dimensions'] = Dimensions;
    	repData['Metrics'] = Metrics;
    	repData['PDate'] = dateVal;
    	repData['Filters'] = Filters;
    	repData['ReportType'] = $('#report-type').html();
    	
		var columns = [];
		var darkBg = [];
		var nbg = 0;
		
		if($('#report-type').html() != 'Overall Report'){
			columns.push({"title": 'Time'});
			darkBg.push(nbg);
	    	nbg++;
		}
			    	    	
    	Dimensions.forEach( function(v, i) {
	    	columns.push({"title": v});
	    	darkBg.push(nbg);
	    	nbg++;
		});
		
		Metrics.forEach( function(v, i) {
			columns.push({"title": v})
		});
				
		if(DExec){
			//console.log('Clear');
			$('#report-table').dataTable().fnDestroy();
			$('#report-table').remove();
			$('#table-container').empty();
		}
		
		$('#table-container').append('<table id="report-table" class="display panel-table" style="width:100%"></table>');
		$('#report-table').hide();
		$('.results-showing').hide();
		$('.results-pagination').hide();
		$('#run-report').attr('disabled',true);
		$('.results-showing').empty();
		$('.results-pagination').empty();
				
		$('#report-table').DataTable( {
			'columns': columns,
			"order": [[ 0, "asc" ]],
	        "processing": true,
	        "serverSide": true,
	        "bLengthChange" : false,
	        "searching": false,
	        "pageLength": 50,
	        "ajax": {
               "url": 'repo.php',
               "type": 	'POST',
               "data": 	repData
           	},
		   	"initComplete":function( settings, json){
				$('.results-showing').append( $('#report-table_info') );
				$('.results-pagination').append( $('#report-table_paginate') );
				
				$('.results-showing').show();
				$('.results-pagination').show();
				$('#report-table').show();
				$('#run-report').attr('disabled',false);
			},
			"columnDefs": [
			    { className: "dark-bg", "targets": darkBg }
			  ]
	    });
		//dark-bg
		DExec = true;
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

    jQuery(function($) {
      $('.dynamicNumber').dynamicNumber({duration: 120000, decimals: 0, format: function format(n, options) { return number_format(n, 0, '.', ','); } });
      
      $('.dynamicNumberPor').dynamicNumber({duration: 120000, decimals: 2, format: function format(n, options) { return number_format(n, 2, '.', ',') + '%'; } });
      
      $('.dynamicNumberMoney').dynamicNumber({duration: 120000, decimals: 2, format: function format(n, options) { return '$' + number_format(n, 2, '.', ','); } });
    });
	
	function updateLive(){
		$.ajax({
		  dataType: "json",
		  url: 'refresh.php'
		}).done(function( res ) {
		    $('#dynRev').dynamicNumber('go', res['Revenue']);
		    $('#dynPro').dynamicNumber('go', res['Profit']);
		    $('#dynImp').dynamicNumber('go', res['Impressions']);
		    $('#dynFlo').dynamicNumber('go', res['formatLoads']);
		    $('#dynFlf').dynamicNumber('go', res['formatLoadFill']);
		    
		    setTimeout(updateLive, 120000);
		});
	}
	
	setTimeout(updateLive, 120000);

</script>
</body>
</html>