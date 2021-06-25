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
	
	if($_SESSION['Admin']!=1){
		header('Location: https://login.vidoomy.com/admin/login.php');
		exit(0);
	}
	
	$idG = $_GET['idg'];
	
	$sql = "SELECT Name FROM demandgroup WHERE id = '$idG'";
	$GruopName = $db->getOne($sql);
	
	if(isset($_GET['d'])){
		$idDe = intval($_GET['d']);
		$sql = "DELETE FROM demandtagrules WHERE id = '$idDe' LIMIT 1";
		$db->query($sql);
		header('Location: group.php?idg=' . $idG);
		exit(0);
	}
	if(isset($_GET['dt'])){
		$idDe = intval($_GET['dt']);
		$sql = "DELETE FROM demandtags WHERE id = '$idDe' LIMIT 1";
		$db->query($sql);
		$sql = "DELETE FROM demandtagrules WHERE idTag = '$idDe' OR idTag = '$idDe' LIMIT 1";
		$db->query($sql);
		header('Location: group.php?idg=' . $idG);
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
<body class="reports conqueror">
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
				<a href="/">Optimización de Demanda</a>
				<span><?php echo $GruopName; ?></span>
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
						<span>Optimización</span>
					</a>
					<ul class="submenu">
						<li class="active">
							<a href="index.php">
								<span class="icon dashboard"></span>
								<span>Tags</span>
							</a>
						</li>
						
					</ul>
				</li>
			</ul>
		</nav>

		<section class="main">
	      <div class="section-header-flex">
	        <div>
	          <h1 class="page-title"><?php
		          $sql = "SELECT Name FROM demandgroup WHERE id = '$idG' LIMIT 1";
		          echo $db->getOne($sql);
	          ?></h1>
	          <p class="page-subtitle"></p>
	        </div>
	        <div>
	          <button class="btn btn-square btn-add popup-trigger btn-new-user"><span class="icon"></span>Añadir Tag</button>
	        </div>
	      </div>

		  <div class="panel panel-table reports-panel">
			  
			  <div class="panel-body">
				  <div class="table-container" id="table-container">
					  
				  </div>
				  
				  <div class="results">
						<div class="results-showing" style="width: 100%;">
							<table id="report-table" class=" panel-table" style="width:100%"><?php
									?>
									<thead>
										<tr>
											
											<th>Nombre</th>
											<th>Tipo</th>
											<th>Tag ID</th>
											<th>List ID</th>
											<th>Min. Fill</th>
											<th>Requests</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody><?php
	            						$sql = "SELECT dt.id AS id, dt.Description AS Name, dt.DemandTagID AS TagID, dt.Open AS Open, dt.DomainListId AS DomainListId, dt.MinFill AS MinFill, dt.MinRequests AS MinRequests 
		            						FROM demandtags dt 
		            						WHERE idGroup = '$idG' 
		            						ORDER BY dt.id ASC";
										$query = $db->query($sql);
										if($db->num_rows($query) > 0){
											while($DTag = $db->fetch_array($query)){
												$idDT = $DTag['id'];
												if($DTag['Open'] == 1){
													$Open = 'Open';
													$ListID = 'NA';
													$MinFill = '';
													$MinReq = '';
												}else{
													$Open = 'White List';
													$ListID = $DTag['DomainListId'];
													$MinFill = $DTag['MinFill'];
													$MinReq = $DTag['MinRequests'];
												}
			            						?><tr>
				            						<td style="background-color:#f8f8f8 !important;"><?php echo $DTag['Name']; ?></td>
				            						<td style="background-color:#f8f8f8 !important;"><?php echo $Open; ?></td>
				            						<td style="background-color:#f8f8f8 !important;"><?php echo $DTag['TagID']; ?></td>
				            						<td style="background-color:#f8f8f8 !important;"><?php echo $ListID; ?></td>
				            						<td style="background-color:#f8f8f8 !important;"><?php echo $MinFill; ?></td>
				            						<td style="background-color:#f8f8f8 !important;"><?php echo $MinReq; ?></td>
				            						<td style="background-color:#f8f8f8 !important;" class="center">
					            						<?php if($DTag['Open'] != 1) { ?>
														<!--<a href="javascript:void(0)" class="icon-small edit popup-trigger btn-new-user"></a>-->
														<a href="javascript:void(0)" data-value="group.php?idg=<?php echo $idG; ?>&dt=<?php echo $idDT; ?>" class="m-hidden icon-small delete popup-trigger btn-delete-tag"></a>
														<?php } else { ?>
														<a href="" class="icon-small" style="visibility: hidden;"></a>
														<?php } ?>
													</td>
			            						</tr>
			            						<?php
				            						$sql = "SELECT COUNT(*) FROM demandtags WHERE idGroup = '$idG' AND Open != 1 AND id != '$idDT'";
			            							if($db->getOne($sql) > 0){
				            							$sql = "SELECT 
					            								demandtagrules.id AS id, 
					            								demandtagrules.KPI AS KPI, 
					            								demandtagrules.Type AS Type, 
					            								demandtags.Description AS TagODescription, 
					            								demandtags.DemandTagID AS TagOId 
					            							FROM demandtagrules 
					            							INNER JOIN demandtags ON demandtags.id = demandtagrules.idTagO 
					            							WHERE demandtagrules.idTag = '$idDT'";
				            							$query3 = $db->query($sql);
														if($db->num_rows($query3) > 0){
															?><tr><td colspan="7"><table style="width: 90%; margin: auto;"><thead>
																	<th>Criterio</th>
																	<th>KPI</th>
																	<th>Tag Objetivo</th>
																	<th></th>
																</thead>
															<tbody><?php
															while($DR = $db->fetch_array($query3)){
						            							?><tr>
							            							<td>Fill</td>
								            						<td><?php echo $DR['KPI']; ?></td>	
								            						<td><?php echo $DR['TagODescription']; ?> (<?php echo $DR['TagOId']; ?>)</td>
								            						<td><a href="javascript:void(0)" data-value="group.php?idg=<?php echo $idG; ?>&d=<?php echo $DR['id']; ?>" class="m-hidden icon-small delete popup-trigger btn-delete-rule"></a></td>	
								            					</tr><?php
							            					}
							            					?></tbody></table></td></tr><?php
							            				}
			            						?>
			            						<tr>
				            						<td colspan="7">
				            							<div class="panel-body">
															<div class="pb-inner" style="width: 90%; margin: auto;">
																<form class="add-tag" id="newrule<?php echo $idDT; ?>">
																	<div class="row" id="row">
																	
																		<div class="form-field">
																			<label for="country1" class="align-middle">Criterio</label>
																			<div class="dropdown form">
																				<input type="hidden" name="criteria" class="hiddenv" value="1">
																				<button type="button" id="criteria">Fill</button>
																				<ul class="dropdown-menu">
																					<li><a href="#" data-value="1">Fill</a></li>
																					<li><a href="#" data-value="2">Impresiones</a></li>
																					<li><a href="#" data-value="3">Requests</a></li>
																				</ul>
																			</div>
																		</div>
																		<div class="form-field">
																			<label for="pv_min">KPI</label>
																			<input type="text" id="kpi_<?php echo $idDT; ?>" class="onlynumberdecimal" name="kpi" placeholder="0">
																		</div>
																		<div class="form-field country-item-wrapper" style="width:35% !important;">
																			<label for="country2" class="align-middle">Tag objetivo</label>
										
																			<div class="dropdown form">
																				<input type="hidden" name="otag" class="hiddenv" value="0">
																				<button type="button" id="otag">Seleccionar</button>
																				<ul class="dropdown-menu">
																				<?php
											            						$sql = "SELECT * FROM demandtags WHERE idGroup = '$idG' AND Open != 1 AND id != '$idDT'";
										            							$query2 = $db->query($sql);
																				if($db->num_rows($query2) > 0){
																					while($DT = $db->fetch_array($query2)){
												            							?><li><a href="#" data-value="<?php echo $DT['id']; ?>"><?php echo $DT['Description']; ?> (<?php echo $DT['DemandTagID']; ?>)</a></li><?php
													            					}
													            				}	
																				?></ul>
																			</div>
																		</div>
																		<input type="hidden" value="<?php echo $idDT; ?>" name="idtag">
																	</div>
																	<input type="hidden" value="3" name="formtype">
																</form>
																<div class="new-country-wrapper"></div>
																<div class="align-right">
																	<button class="btn btn-add add-country-btn form-submit new-rule"  data-value="<?php echo $idDT; ?>" type="button">Añadir regla<span class="icon"></span></button>
																</div>
															</div>
														</div>
				            						</td>
			            						</tr><?php
				            					}
											}
										}
									?></tbody>
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
			<div class="popup-container popup-delete popup-delete-rule">
		        <div class="popup-body">
		            <div class="info align-center">
						<span class="icon delete"></span>
						<p>¿Estas seguro de borrar esta regla?</p>
						<p></p>
					</div>
					<div class="form-field">
						<div class="align-center">
							<a href="#" class="btn btn-square close-btn">Cancelar</a>
							<a href="" id="del-rule-id" class="btn btn-square flat-btn">Sí</a>
						</div>
					</div>
      			</div>
	  		</div>
	  		<div class="popup-container popup-delete popup-delete-tag">
		        <div class="popup-body">
		            <div class="info align-center">
						<span class="icon delete"></span>
						<p>¿Estas seguro de borrar este tag?</p>
						<p></p>
					</div>
					<div class="form-field">
						<div class="align-center">
							<a href="#" class="btn btn-square close-btn">Cancelar</a>
							<a href="" id="del-tag-id" class="btn btn-square flat-btn">Sí</a>
						</div>
					</div>
      			</div>
	  		</div>
			<div class="popup-container popup-edit">
	          <div class="popup-header">
	            <h4><span class="icon"></span>Nuevo Tag</h4>
	            <a href="javascript:void(0)" class="close-btn"></a>
	          </div>
	          <div class="popup-body">
	            <div class="info">
	              <h3 id="popwebname">Añadir Tag</h3>
	            </div>
	            <hr>
	            <form id="newtag">
				  <p class="check-message"></p>
	              <div class="form-field">
	                <label for="divid"><span class="required">*</span>Nombre</label>
	                <input type="text" id="description" name="description">
	              </div>
	              <div class="form-field">
	                <label for="line"><span class="required">*</span>Tag ID</label>
	                <input type="text" id="tagid" name="tagid" class="onlynumber" style="width:140px;">
	              </div>
	              <div class="form-field">
	                <label for="line"><span class="required">*</span>Domain List ID</label>
	                <input type="text" id="listid" name="listid" class="onlynumber" style="width:140px;">
	              </div>
	              <div class="form-field">
	                <label for="line"><span class="required"></span>Min. Fill</label>
	                <input type="text" id="fill" name="fill" class="onlynumberdecimal" style="width:80px;">
	              </div>
	              <div class="form-field">
	                <label for="line"><span class="required"></span>Requests</label>
	                <input type="text" id="requests" name="requests" class="onlynumber" style="width:80px;">
	              </div>        
					<hr>
					<div class="form-field">
						<div class="align-right">
							<a href="javascript:void(0)" class="btn btn-square close-btn">Cancelar</a>
							<a href="javascript:void(0)" class="btn btn-square flat-btn form-submit new-tag">Guardar cambios</a>
						</div>
					</div>
					<input type="hidden" value="<?php echo $idG; ?>" name="idgroup">
					<input type="hidden" value="2" name="formtype">
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


// Restricts input for each element in the set of matched elements to the given inputFilter.
(function($) {
  $.fn.inputFilter = function(inputFilter) {
    return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function() {
      if (inputFilter(this.value)) {
        this.oldValue = this.value;
        this.oldSelectionStart = this.selectionStart;
        this.oldSelectionEnd = this.selectionEnd;
      } else if (this.hasOwnProperty("oldValue")) {
        this.value = this.oldValue;
        this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
      }
    });
  };
}(jQuery));

$(document).ready(function() {
  // Restrict input to digits by using a regular expression filter.
  $(".onlynumber").inputFilter(function(value) {
    return /^\d*$/.test(value);
  });
  $(".onlynumberdecimal").inputFilter(function(value) {
    return /^-?\d*[.]?\d*$/.test(value);
  });
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