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
	
	if(@$_SESSION['Admin'] >= 1){
		if(isset($_GET['siteid'])){
			$siteId = intval($_GET['siteid']);
			if($siteId > 0){
				$sql = "SELECT * FROM " . SITES . " WHERE id = '$siteId' LIMIT 1";
				$query = $db->query($sql);
				if($db->num_rows($query) > 0){
					$siteData = $db->fetch_array($query);
					$new = false;
					if(isset($_GET['new'])){
						if($_GET['new'] == 1){
							$new = true;
						}
					}
					$idUser = $siteData['idUser'];
					
					$adstxt = '';
					$nl = "";
					$sql = "SELECT LKQD_id FROM " . USERS . " WHERE id = '$idUser' LIMIT 1";
					$idLkqd = $db->getOne($sql);
					$sql = "SELECT * FROM " . ADSTXT . " ORDER BY id ASC";
					$query = $db->query($sql);
					if($db->num_rows($query) > 0){
						while($Line = $db->fetch_array($query)){
							$LineTxt = str_replace('{LKQDID}', $idLkqd, $Line['LineTxt']);
							$adstxt .= $nl . $LineTxt;
							$nl = "\n";
						}
					}
/*
	
	$adstxt = 'vidoomy.com, 183'.$siteData['idUser'].', DIRECT
lkqd.net, 430, RESELLER, 59c49fa9598a0117
lkqd.com, 430, RESELLER, 59c49fa9598a0117
aolcloud.net, 22762, RESELLER
advertising.com, 22762, RESELLER
sunmedia.tv,101,RESELLER,24794F6
aol.com, 22762, RESELLER
Aol.com, 8982, RESELLER
pubmatic.com, 156498, RESELLER, 5d62403b186f2ace
beachfront.com, 6547, RESELLER #Vidoomy
beachfront.com, beachfront_2207, RESELLER
fyber.com, 1236fbff61fdf8859e37831848a42ee5, RESELLER
tremorhub.com, 4cywq-a04wk, RESELLER, 1a4e959a1b50034a
Freewheel.tv, 195713, RESELLER
google.com, pub-1937576406332709, RESELLER, f08c47fec0942fa0
google.com, pub-5512390705137507, RESELLER, f08c47fec0942fa0
google.com, pub-7683628640306220, RESELLER, f08c47fec0942fa0
google.com, pub-8221793852898543, RESELLER, f08c47fec0942fa0
google.com, pub-1968276357835282, RESELLER, f08c47fec0942fa0
google.com, pub-2393320645055022, RESELLER, f08c47fec0942fa0
google.com, pub-8856559311549217, RESELLER, f08c47fec0942fa0
google.com, pub-4983172728561290, RESELLER, f08c47fec0942fa0
tremorhub.com, cpu32-92xut, RESELLER, 1a4e959a1b50034a
spotxchange.com,151986,RESELLER,7842df1d2fe2db34
spotx.tv,151986,RESELLER,7842df1d2fe2db34
spotx.tv,202100,RESELLER,7842df1d2fe2db34
spotxchange.com,202100,RESELLER,7842df1d2fe2db34
pubmatic.com, 156136, RESELLER, 5d62403b186f2ace
smartadserver.com, 2079, RESELLER
spotxchange.com, 137584, RESELLER, 7842df1d2fe2db34
spotx.tv, 74844, RESELLER, 7842df1d2fe2db34
spotx.tv, 137584, RESELLER, 7842df1d2fe2db34
google.com, pub-9134075993162501, RESELLER, f08c47fec0942fa
adspruce.com, 4108-3, DIRECT
smartadserver.com, 2079, RESELLER
Aol.com, 8982, RESELLER 
spotxchange.com, 218947, RESELLER, 7842df1d2fe2db34
spotx.tv, 218947, RESELLER, 7842df1d2fe2db34
spotxchange.com, 218945, RESELLER, 7842df1d2fe2db34
spotx.tv, 218945, RESELLER, 7842df1d2fe2db34
spotxchange.com, 202100, RESELLER, 7842df1d2fe2db34
spotx.tv, 202100, RESELLER, 7842df1d2fe2db34
google.com, pub-8221793852898543, RESELLER, f08c47fec0942fa0
google.com, pub-1968276357835282, RESELLER, f08c47fec0942fa0
tremorhub.com, cpu32-92xut, RESELLER, 1a4e959a1b50034a
pubmatic.com, 156136, RESELLER, 5d62403b186f2ace
pubmatic.com, 156509, RESELLER, 5d62403b186f2ace
advertising.com, 19798, RESELLER
pubmatic.com, 156458, RESELLER, 5d62403b186f2ace
pubmatic.com, 156325, RESELLER, 5d62403b186f2ace
pubmatic.com, 156084, RESELLER, 5d62403b186f2ace
indexexchange.com, 183965, RESELLER, 50b1c356f2c5c8fc
coxmt.com, 2000067995202, RESELLER
sunmedia.tv, 101, DIRECT, 24794F6
ooyala.com, df0c742b-92ba-4ddb-b9b3-a33991073acd, RESELLER
ooyala.com, 37bb3f3c-1bb8-4b52-85a4-cbfd626b21f4, RESELLER
freewheel.tv, 435713, RESELLER
freewheel.tv, 435745, RESELLER
freewheel.tv, 435777, RESELLER
freewheel.tv, 435809, RESELLER
smartadserver.com, 1999, RESELLER
indexexchange.com, 179394, RESELLER
pulsepoint.com, 560288, RESELLER
pubmatic.com, 156439, RESELLER
rubiconproject.com, 16114, RESELLER
openx.com, 537149888, RESELLER, a698e2ec38604c6
improvedigital.com, 115, RESELLER  
google.com, pub-4597105439779983, RESELLER
google.com, pub-6678849566450770, RESELLER
exponential.com, 155490, RESELLER
tribalfusion.com, 155490, RESELLER
indexexchange.com, 183554, RESELLER
improvedigital.com, 543, RESELLER
openx.com, 539620353, RESELLER, 6a698e2ec38604c6 
pubmatic.com, 156546, RESELLER, 5d62403b186f2ace
smartadserver.com, 2951, DIRECT
contextweb.com,560288, DIRECT, 89ff185a4c4e857c
pubmatic.com, 154037, DIRECT, 5d62403b186f2ace
rubiconproject.com, 16114, DIRECT, 0bfd66d529a55807
sovrn.com, 257611, DIRECT, fafdf38b16bf6b2b
appnexus.com, 3703, DIRECT, f5ab79cb980f11d1
adtech.com, 10466, RESELLER
spotxchange.com, 211738, RESELLER, 7842df1d2fe2db34
spotx.tv, 211738, RESELLER, 7842df1d2fe2db34';
*/

	$adstxt = nl2br($adstxt);
	
	$buscar = array(chr(13).chr(10), "\r\n", "\n", "\r");
	$reemplazar = array("", "", "");
	$adstxt = str_ireplace($buscar,$reemplazar,$adstxt);
	
	$adstxt = str_replace('<br />', "\r", $adstxt);
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
                                        
					<!--<main>-->
					<main class="clmc12 c-flx1">
						<!--<Estadisticas Avanzadas>-->
						<div class="bx-cn bx-shnone">
							<div class="bx-hd dfl b-fx">
								<div class="titl"><?php if($new) { ?>Sitio creado con éxito!<?php }else{ ?>Código para el sitio web<?php } ?></div>
							</div>
							<div class="bx-bd">
								<div class="bx-pd">
									<p>A continuación te mostramos el código que podrás integrar en tu página web.</p>
									<ul class="lst-codg">
										<li>
											<div class="b-fx">
												<figure>
                                                    <!--<h3>PRE-ROLL PC</h3>-->
                                                    <img src="img/cnt/pre-roll-pc.png" alt="">
                                                </figure>
												<div class="b-flx1">
													<button class="btn-blk fa-copy" onclick="copiarAlPortapapeles('tocopy')" style="display:inline-block !important;">Click aqui para copiar el código</button>
													<a class="btn-blk" href="edit-page.php?idpage=<?php echo $siteId; ?>">Editar Página</a>
													<textarea cols="66" rows="6">&#60;script type="text/javascript" src="<?php echo str_replace('http://',"//",$siteData['filename']); ?>" &#62;&#60;/script&#62;</textarea>
													<input type="hidden" name="tocopy" id="tocopy" value='<script type="text/javascript" src="<?php echo str_replace('http://',"//",$siteData['filename']); ?>"></script>' />
													
													ads.txt:<br/>
													<textarea cols="66" rows="6"><?php echo $adstxt; ?></textarea>
													<button class="btn-blk fa-copy" onclick="copiarAlPortapapeles('tocopy2')">Click aqui para copiar ads.txt</button>
													<input type="hidden" name="tocopy2" id="tocopy2" value='<?php echo $adstxt; ?>' />
												</div>
											</div>
										</li>
									</ul>
								</div>
							</div>
						</div>
						<!--</Estadisticas Avanzadas>-->
					</main>
					<!--<main>-->
					
				</div>
				<!--</cls>-->
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
	<script type="text/javascript">
	
	function copiarAlPortapapeles(id_elemento) {
		var aux = document.createElement("input");
		aux.setAttribute("value", document.getElementById(id_elemento).value);
		document.body.appendChild(aux);
		aux.select();
		document.execCommand("copy");
		document.body.removeChild(aux);	
	}
	</script>
</body>
</html><?php
				}else{
					header('Location: pages.php');
					exit(0);
				}
			}else{
				header('Location: pages.php');
				exit(0);
			}
		}else{
			header('Location: pages.php');
			exit(0);
		}
	}else{
		header('Location: index.php');
		exit(0);
	}
?>