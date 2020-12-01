<?php
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
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="js/assets/jquery.magicsearch.css">
<link rel="stylesheet" href="css/main.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<style>
.rm-filter-btn {
	position: absolute;
	width: 21px;
	height: 21px;
	border-radius: 50%;
	border: 1px solid #cbd1de;
	top: 6px;
	right: 10px;
}	
.rm-filter-btn:after {
	content: '';
	width: 9px;
	height: 1px;
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%);
	background-color: #cbd1de;
}
.multi-item{
	padding-top:4px !important;
}
.dropdown button:after, .slide-panel button:after{
	height: 80% !important;
}
</style>
<script>(function(h){h.className=h.className.replace(/\bno-js\b/,'')})(document.documentElement);</script>
<!--[if lt IE 9]>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
<![endif]-->
</head>
<body class="home">
	<!-- <div class="design"></div> -->
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
	<div class="my-container">
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
			<div class="breadcrumba">
				<a href="/">Reportes Personalizados</a>
			</div>
			<div class="notif-bell">
				<a href="javascript:void(0)" class="bell-trigger new"></a>
				<div class="notifications-dropdown">
					
				</div>
			</div>
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
				<li class="active">
					<a href="javascript:void(0)">
						<span class="icon monetization"></span>
						<span>Reportes</span>
					</a>

				</li>
			</ul>
		</nav>

		<section class="main home">
			
			<div class="row-featured">
				<div class="item red">
					<div class="img-wrapper">
						<img src="img/feat-red.png" alt="">
						<div class="img-tooltip">
							<p>15,765,567</p>
							<p>21 Dic 2018</p>
						</div>
					</div>
					<h2>15,765,567</h2>
					<p class="subtitle">Impresiones</p>
				</div>
				<div class="item purple2">
					<div class="img-wrapper">
						<img src="img/feat-purple2.png" alt="">
						<div class="img-tooltip">
							<p>572,241</p>
							<p>21 Dic 2018</p>
						</div>
					</div>
					<h2>572,241</h2>
					<p class="subtitle">Clicks</p>
				</div>
				<div class="item purple">
					<div class="img-wrapper">
						<img src="img/feat-purple.png" alt="">
						<div class="img-tooltip">
							<p>13.76%</p>
							<p>21 Dic 2018</p>
						</div>
					</div>
					<h2>13.76%</h2>
					<p class="subtitle">CTR</p>
				</div>
				<div class="item green">
					<div class="img-wrapper">
						<img src="img/feat-green.png" alt="">
						<div class="img-tooltip">
							<p>$8,140.02</p>
							<p>21 Dic 2018</p>
						</div>
					</div>
					<h2>$8,140.02</h2>
					<p class="subtitle">Revenue</p>
				</div>
				<div class="item orange">
					<div class="img-wrapper">
						<img src="img/feat-orange.png" alt="">
						<div class="img-tooltip">
							<p>$1.95</p>
							<p>21 Dic 2018</p>
						</div>
					</div>
					<h2>$1.95</h2>
					<p class="subtitle">eCPM</p>
				</div>
			</div>
			
			<div class="top-info">
				<h1 class="page-title">Reports</h1>
				<p class="page-subtitle"></p>

				<div class="date-wrapper">
					<div id="reportrange">
						<span class="date"><span class="icon"></span> Date</span>
						<span class="from">From <span></span></span>
						<span class="to">To <span></span></span>
					</div>
				</div>
			</div>
				
			<div class="row-featured">
				<div class="panel-body item">
					<div id="dimensions">
					    <div class="dropdown" id="klon1">
						    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" style="margin-bottom: 10px;">+ Dimesion</button>
						    <ul class="dropdown-menu" role="menu" aria-labelledby="Dimension">
							      <li class="dropdown-header">Supply</li>
							      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Supply Source</a></li>
							      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Supply Partner</a></li>
							      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Domain</a></li>
							      <!--<li class="dropdown-header">Demand</li>  
							      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Demand Partner</a></li>
							      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Demand Order</a></li>
							      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Demand Deal</a></li>
							      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Demand Tag</a></li>
							      <li class="dropdown-header">Execution</li>
							      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Environment</a></li>-->
							      <li class="dropdown-header">Geography</li>
							      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Country</a></li>
						    </ul>
						</div>
					</div>	
					<p class="subtitle" style="position: absolute; bottom: 3px; right:10px;">Selected Dimensions</p>
				</div>
			
				<div class="panel-body item">
					<div id="metrics">
					    <div class="dropdown" id="mmm1">
						    <button class="btn btn-warning dropdown-toggle" id="Metric" type="button" data-toggle="dropdown" style="margin-bottom: 10px;">+ Metric</button>
						    <ul class="dropdown-menu" role="menu" aria-labelledby="Metric">
							      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Format Loads</a></li>
							      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Impressions</a></li>
							      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Format Load Fillrate</a></li>
							      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Opportunities</a></li>
							      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">CPM</a></li>
							      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Revenue</a></li>
							      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Extraprima Cost</a></li>
							      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Media Cost</a></li>
							      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Profit</a></li>
							      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Profit Margin</a></li>
							      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">CTR</a></li>
							      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">VTR</a></li>
							      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Viewability Rate</a></li>
						    </ul>
						</div>
					</div>
					<p class="subtitle" style="position: absolute; bottom: 3px; right:10px;">Selected Metrics</p>
				</div>
			</div>
			
			<div class="row-featured">
				<div class="panel-body item">
					<p class="subtitle" style="position: absolute; bottom: 3px; right:10px;">Filters</p>
					
					<div id="filters" style="width: 50%;">
						

					</div>
					
					<br style="clear:both;" />
					<div id="filters">
					    <div class="dropdown" id="addfilter">
						    <button class="btn btn-danger dropdown-toggle" type="button" data-toggle="dropdown" style="margin-bottom: 10px;">+ Filter</button>
						    <ul class="dropdown-menu" role="menu" aria-labelledby="Filter">
							      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Supply Source</a></li>
							      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Supply Partner</a></li>
							      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Domain</a></li>
							      <!--<li class="dropdown-header">Demand</li>  
							      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Demand Partner</a></li>
							      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Demand Order</a></li>
							      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Demand Deal</a></li>
							      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Demand Tag</a></li>
							      <li class="dropdown-header">Execution</li>
							      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Environment</a></li>-->
							      <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Country</a></li>
						    </ul>
						</div>
					</div>
					
					
				</div>
			</div>
			
				<button class="btn btn-primary" id="run-report" type="button" style="margin-top:10px; float:right;">Run Report</button>
				<br style="clear:both;" />
			
			<!--<div class="panel panel-chart panel-publishers">
				<div class="panel-header">
					<ul class="tabs">
						<li class="active">Date</li>
						<li>Website</li>
						<li>Country</li>
						<li>Device</li>
						<li>OS</li>
					</ul>
					<div class="help">
					  <p class="help-inner right">Adofi asodiuao weior qpoeb utlos as</p>
					</div>
				</div>
				<div class="panel-body chart-container">
					<div class="chart-legend"></div>
					<div class="chart-box">
						<canvas id="chart"></canvas>
					</div>
					<div class="chart-months">
						<span class="from-month"></span> - <span class="to-month"></span>
					</div>
				</div>
			</div>-->

			<div class="panel panel-table">
				<div class="panel-body">
					<div class="table-container" id="table-container">
					</div>
					<!-- <div class="results">
						<div class="results-showing">
							Showing 1 to 10 of 23 entries
						</div>
						<div class="results-pagination">
							<a href="javascript:void(0)">Previous</a>
							<a href="javascript:void(0)">1</a>
							<a href="javascript:void(0)">2</a>
							<a href="javascript:void(0)">3</a>
							<a href="javascript:void(0)">Next</a>
						</div>
					</div> -->
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

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
<!-- <script src="js/assets/Chart.bundle.min.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.bundle.min.js"></script>
<script type="text/javascript" src="js/assets/daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="js/assets/daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="js/assets/jquery.magicsearch.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="js/main.js"></script>
<script>
	function addDimension(t){
		if($('div[id^="klon"]:last').length > 0){
	    	var $div = $('div[id^="klon"]:last');
	    	var num = parseInt( $div.prop("id").match(/\d+/g), 10 ) +1;
    	} else {
			num = 1;
		}
		
		$('#dimensions .dropdown:last-child ul a').unbind("click");
		
    	var $newDrop = $('#dimensions .dropdown:last-child').clone().prop('id', 'klon' + num);
    	$('#dimensions .dropdown:last-child ul').prepend('<li role="presentation"><a role="menuitem" tabindex="-1" href="#" class="delDim">- Dimension</a></li>');
    	$('#dimensions .dropdown:last-child ul a.delDim').click(function(){
	    	$(this).parents('.dropdown').remove();
    	});
    	
    	$('#dimensions .dropdown:last-child ul a').click(function(){
	    	var newVal = $(this).html();
	    	$('#dimensions .dropdown button').each(function(i, obj) {
				if(newVal == $(this).html()){
					$(this).parent().remove();
				}
			});
    	});
    	
    	var Add = true;
    	$('#dimensions .dropdown button').each(function(i, obj) {
			if(t == $(this).html()){
				$(this).parent().remove();
			}
		});

	    $newDrop.appendTo( "#dimensions" );
	    $('#klon' + num + ' a').click(function(){ addDimension($(this).html()); });
	}
	
	$('#klon1 a').click(function(){
		addDimension($(this).html());
	});
	
	function addMetric(t){
		if($('div[id^="mmm"]:last').length > 0){
	    	var $div = $('div[id^="mmm"]:last');
	    	var nu = parseInt( $div.prop("id").match(/\d+/g), 10 ) +1;
    	} else {
			var nu = 1;
		}
		
		$('#metrics .dropdown:last-child ul a').unbind("click");
		
    	var $newDrop = $('#metrics .dropdown:last-child').clone().prop('id', 'mmm' + nu);
    	$('#metrics .dropdown:last-child ul').prepend('<li role="presentation"><a role="menuitem" tabindex="-1" href="#" class="delMet">- Metric</a></li>');
    	$('#metrics .dropdown:last-child ul a.delMet').click(function(){
	    	$(this).parents('.dropdown').remove();
    	});
    	
    	$('#metrics .dropdown:last-child ul a').click(function(){
	    	var newVal = $(this).html();
	    	$('#metrics .dropdown button').each(function(i, obj) {
				if(newVal == $(this).html()){
					$(this).parent().remove();
				}
			});
    	});
    	
    	var Add = true;
    	$('#metrics .dropdown button').each(function(i, obj) {
			if(t == $(this).html()){
				$(this).parent().remove();
			}
		});
		
	    $newDrop.appendTo( "#metrics" );
	    $('#mmm' + nu + ' a').click(function(){ addMetric($(this).html()); });
	}
	
	$('#mmm1 a').click(function(){
		addMetric($(this).html());
	});
	
	
	//FILTERS
	$('#addfilter .dropdown-menu li a').click(function(e){
		e.preventDefault();
		var filterName = $(this).html();
		$('#filters').append('<div class="form-field web-item-wrapper"><label for="web-page3">'+filterName+'</label><input type="text" id="web-page3" placeholder="" name="web-page3" class="web-name not-required"><a href="javascript:void(0)" class="rm-filter-btn"></a></div>');
		
		$(this).parent().hide();
		
		$('#addfilter button').val('+ Filter');
		
		$('.rm-filter-btn').click(function(){
			$('#addfilter .dropdown-menu li a').each(function(){
				if($(this).html() == filterName){
					$(this).parent().show();
				}
			});
			
			$(this).parent().remove();
		});
		

		
		$.ajax({
		    type: 'POST',
		    url: 'acom.php',
		    data: { 
		        'filter': filterName
		    },
		    success: function(dataSource){
			    //var dataSource = $.parseJSON(data);
		        
		        $('#filters div:last-child .web-name').magicsearch({
	                dataSource: dataSource,
	                fields: ['tagName'],
	                id: 'id',
	                format: '%tagName%',
	                multiple: true,
	                multiField: 'tagName',
	                multiStyle: {
	                    space: 5,
	                    width: 160
	                }
	            });
		        
		    }
		});

		setTimeout(function(){$('#addfilter button').html('+ Filter');}, 100);
	});
	
	
	
	DExec = false;
	//REPORT SENDING
	$('#run-report').click(function(){
		var bVal = '';
		var fVal = '';
		var Dimensions = [];
		var Metrics = [];
		var dateVal = [];
		var repData = [];
		var Filters = [];
		if($('div[id^="klon"]').length > 0){
	    	$('div[id^="klon"]').each(function(i, obj) {
				bVal = $(this).find('button').html();
				if(bVal.charAt(0) != '+'){
					Dimensions.push(bVal);
				}
			});
    	}
    	if($('div[id^="mmm"]').length > 0){
	    	$('div[id^="mmm"]').each(function(i, obj) {
				bVal = $(this).find('button').html();
				if(bVal.charAt(0) != '+' && bVal.charAt(0) != '-'){
					Metrics.push(bVal);
				}
			});
    	}
    	
    	if($('#filters .web-item-wrapper').length > 0){
	    	$('#filters .web-item-wrapper').each(function(i, obj) {
				fVal = $(this).find('label').html();
				console.log('LAB: '+fVal);
				$(this).find('.multi-item span').each(function(i, obj) {
					Filters.push({label: fVal, value: $(this).html()});
				});
			});
    	}
    	
    	console.dir(Filters);
    	
    	dateVal.push($('#reportrange .from span').html());
    	dateVal.push($('#reportrange .to span').html());
    	
    	repData['Dimensions'] = Dimensions;
    	repData['Metrics'] = Metrics;
    	repData['PDate'] = dateVal;
    	repData['Filters'] = Filters;
    			
		var columns = [];
			    	    	
    	Dimensions.forEach( function(v, i) {
	    	columns.push({"title": v})
		});
		
		Metrics.forEach( function(v, i) {
			columns.push({"title": v})
		});
				
		if(DExec){
			console.log('Clear');
			$('#report-table').dataTable().fnDestroy();
			$('#report-table').remove();
			$('#report-container').empty();
		}
		
		$('#table-container').append('<table id="report-table" class="display" style="width:100%"></table>');
				
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
               "type": 'POST',
               "data": repData
           }
	        
	    });
		
		DExec = true;
	    
	});
</script>
</body>
</html>