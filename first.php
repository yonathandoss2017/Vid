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
	
	if(isset($_SESSION['login'])){
		$sql = "SELECT lang FROM " . USERS . " WHERE id = '" . $_SESSION['login'] . "' LIMIT 1";
		$lang = $db->getOne($sql);
		//echo '<!--'.$sql.'-->';
		
		if(file_exists('/var/www/html/site/slider/langs/'.$lang.'.php')){
			include('/var/www/html/site/slider/langs/'.$lang.'.php');
		}else{
			//echo $lang . 'NO';
			include('/var/www/html/site/slider/langs/en.php');
		}
		
?><!doctype html>
<html lang="es">
<head>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-KDK6GTQ');</script>
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
								<div class="titl"><?php echo $Texts['WelcomeTxt1']; ?></div>
							</div>
							<div class="bx-bd">
								<div class="bx-pd">
									<div class="bx-hd dfl b-fx">
										<div class="titl"><?php echo $Texts['WelcomeTxt2']; ?></div>
										<p><?php echo $Texts['WelcomeTxt3']; ?><br/><br/></p>
										<ol>
										<li><?php echo $Texts['WelcomeTxt4']; ?><br/><br/></li>
										<li><?php echo $Texts['WelcomeTxt5']; ?><br/><br/></li>
										<li><?php echo $Texts['WelcomeTxt6']; ?> <a href="https://www.vidoomy.com/slider_test" target="_blank">https://www.vidoomy.com/slider_test</a>. <?php echo $Texts['WelcomeTxt7']; ?><br/><br/><br/></li>
										
										</ol>
										
										<div class="frm-group" style="text-align:center; margin:auto;">
											<button class="md-20 login-btn" type="submit" onclick="location.href='stats.php'"><?php echo $Texts['WelcomeBtn']; ?></button>
										</div>
										
									</div>
                                    <div class="clsd-fx">
                                    	<div class="clmd12">
                                        	
                                        </div>
                                    </div>
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
	
</body>
</html><?php
	}else{
		header('Location: index.php');
		exit(0);
	}
?>