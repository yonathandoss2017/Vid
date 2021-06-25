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
	
	/*
	if($_SESSION['Admin']!=1){
		header('Location: https://reports.vidoomy.com/admin/login.php');
		exit(0);
	}
	*/
	if(isset($_GET['d'])){
		$idDg = intval($_GET['d']);
		
		$sql = "SELECT * FROM demandtags WHERE idGroup = '$idDg' ";
		$query = $db->query($sql);
		if($db->num_rows($query) > 0){
			while($DTag = $db->fetch_array($query)){
				$idDe = $DTag['id'];
				$sql = "DELETE FROM demandtagrules WHERE idTag = '$idDe' OR idTag = '$idDe' ";
				$db->query($sql);
			}
		}
		
		$sql = "DELETE FROM demandtags WHERE idGroup = '$idDg' ";
		$db->query($sql);
		
		$sql = "DELETE FROM demandgroup WHERE id = '$idDg' ";
		$db->query($sql);
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
				<a href="/">Optimización de Demanda</a>
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
	          <h1 class="page-title">Optimización de Demanda</h1>
	          <p class="page-subtitle"></p>
	        </div>
	        <div>
	          <button class="btn btn-square btn-add popup-trigger btn-new-user"><span class="icon"></span>Crear Tag Group</button>
	        </div>
	      </div>

		  <div class="panel panel-table reports-panel">
			  
			  <div class="panel-body">
				  <div class="table-container" id="table-container">
					  
				  </div>
				  
				  <div class="results">
						<div class="results-showing" style="width: 100%; overflow: auto;">
							<table id="report-table" class=" panel-table" style="width:100%"><?php
									?>
									<thead>
										<tr>
											
											<th>Nombre</th>
											<th>Pais</th>
											<th>Creado</th>
											<th>Active</th>
											<th></th>
										</tr>
									</thead>
									<tbody><?php
	            						$sql = "SELECT dg.id AS id, c.country_name AS Country, dg.Name AS Name, dg.Added AS Added, dg.Active AS Active FROM demandgroup dg
	            						INNER JOIN countries c ON c.id = dg.Country
	            						ORDER BY dg.id DESC";
										$query = $db->query($sql);
										if($db->num_rows($query) > 0){
											while($DG = $db->fetch_array($query)){
												$idDG = $DG['id'];
												
												$sql = "SELECT COUNT(*) FROM demandrules WHERE idGroup = '$idDG'";
					            				$TagsN = $db->getOne($sql);
					            				
												$Action = 'enabledisable';

					            				if($DG['Active'] == 1){ 
						            				$Status = '';
						            				$Text = 'Activo';
					            				}else{
						            				$Status = ' pending'; 
						            				$Text = 'Inactivo';
						            			}
												
			            						?><tr>
				            						<td> <a href="group.php?idg=<?php echo $idDG; ?>"><?php echo $DG['Name']; ?></a></td>
				            						<td><?php echo $DG['Country']; ?></td>
				            						<td><?php echo $DG['Added']; ?></td>
				            						<td><a href="chstate.php?idg=<?php echo $idDG; ?>" class="<?php echo $Action; ?>"><span class="payment-icon<?php echo $Status; ?>"></span><span class="m-hidden"><?php echo $Text; ?></span></a></td>
				            						<td><a href="javascript:void(0)" data-value="index.php?d=<?php echo $idDG; ?>" class="m-hidden icon-small delete popup-trigger btn-delete-group"></a></td>
				            						<?php
			            						?></tr><?php
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
				<div class="popup-container popup-delete popup-delete-group">
			        <div class="popup-body">
			            <div class="info align-center">
							<span class="icon delete"></span>
							<p>¿Estas seguro de borrar este grupo?</p>
							<p></p>
						</div>
						<div class="form-field">
							<div class="align-center">
								<a href="#" class="btn btn-square close-btn">Cancelar</a>
								<a href="" id="del-group-id" class="btn btn-square flat-btn">Sí</a>
							</div>
						</div>
          			</div>
		  		</div>
				
				<div class="popup-container popup-edit">
		          <div class="popup-header">
		            <h4><span class="icon"></span>Nuevo Tag Group</h4>
		            <a href="javascript:void(0)" class="close-btn"></a>
		          </div>
		          <div class="popup-body">
		            <div class="info">
		              <h3 id="popwebname">Crear nuevo Grupo de Tags</h3>
		            </div>
		            <hr>
		            <form id="newgroup">
					  <p class="check-message"></p>
		              <div class="form-field">
		                <label for="divid"><span class="required">*</span>Nombre</label>
		                <input type="text" id="groupname" name="groupname">
		              </div>
		              <div class="form-field">
		                <label for="line"><span class="required">*</span>Open Tag ID</label>
		                <input type="text" id="grouptagid" name="grouptagid" style="width:100px;">
		              </div>
		              <div class="form-field">
						<label for="country1" class="align-middle"><span class="required">*</span>País</label>

						<div class="dropdown form">
							<input type="hidden" name="country" class="hiddenv" value="0">
							<button type="button" id="country">General</button>
							<ul class="dropdown-menu"><?php
								$sql = "SELECT * FROM countries ORDER BY id ASC";
								$query = $db->query($sql);
								if($db->num_rows($query) > 0){
									while($Country = $db->fetch_array($query)){
										?><li><a href="#" data-value="<?php echo $Country['id']; ?>"><?php echo $Country['country_name']; ?></a></li><?php
									}
								}
							?>
							</ul>
						</div>
					  </div>       
						<hr>
						<div class="form-field">
							<div class="align-right">
								<a href="javascript:void(0)" class="btn btn-square close-btn">Cancelar</a>
								<a href="javascript:void(0)" class="btn btn-square flat-btn form-submit new-group">Guardar cambios</a>
							</div>
						</div>
						<input type="hidden" value="1" name="formtype">
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

<script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="js/main.js"></script>
<script>
	$('.enabledisable').click(function(e){		
		e.preventDefault();
		var link = $(this);
		var url = $(this).attr('href');
		var jqxhr = $.get(url , function(a) {
		  if(a == 1){
			  link.children('span.payment-icon').removeClass('pending');
			  link.children('span.m-hidden').html('Activo');
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
  $("#grouptagid").inputFilter(function(value) {
    return /^\d*$/.test(value);
  });
});

</script>
</body>
</html>