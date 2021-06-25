<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$N = intval($_GET['n']);
	
	echo "<div style='font-family:Arial;'>";
	
	$sql = "SELECT * FROM dev_test_front WHERE Last = 'L$N'";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Tt = $db->fetch_array($query)){
			echo "Start: " . date('d/m/Y H:i:s', $Tt['Start']) . "<br/>";
			echo "Test 1: " . date('d/m/Y H:i:s', $Tt['Submit1']) . " <a href='http://www.vidoomy.com/dev_test_front/results/test_" . $Tt['id'] . "_1.js'>Ver</a> <a href='http://www.vidoomy.com/dev_test_front_en/results/test_" . $Tt['id'] . "_1.js'>View</a><br/>";
			echo "Test 2: " . date('d/m/Y H:i:s', $Tt['Submit2']) . " <a href='http://www.vidoomy.com/dev_test_front/results/test_" . $Tt['id'] . "_2.js'>Ver</a> <a href='http://www.vidoomy.com/dev_test_front_en/results/test_" . $Tt['id'] . "_2.js'>View</a> - <a href='http://www.vidoomy.com/dev_test_front/jstest/js_" . $Tt['id'] . ".html'>HTML</a> <a href='http://www.vidoomy.com/dev_test_front_en/jstest/js_" . $Tt['id'] . ".html'>HTML EN</a><br/>";
			echo "Test 3: " . date('d/m/Y H:i:s', $Tt['Submit3']) . " <a href='http://www.vidoomy.com/dev_test_front/results/test_" . $Tt['id'] . "_3.js'>Ver</a> <a href='http://www.vidoomy.com/dev_test_front_en/results/test_" . $Tt['id'] . "_3.js'>View</a><br/>";
			echo "Test 4: " . date('d/m/Y H:i:s', $Tt['Submit4']) . " <a href='http://www.vidoomy.com/dev_test_front/results/test_" . $Tt['id'] . "_4.js'>Ver</a> <a href='http://www.vidoomy.com/dev_test_front_en/results/test_" . $Tt['id'] . "_4.js'>View</a><br/>";
			echo "Test 5: " . date('d/m/Y H:i:s', $Tt['Submit5']) . " <a href='http://www.vidoomy.com/dev_test_front/results/test_" . $Tt['id'] . "_5.js'>Ver</a> <a href='http://www.vidoomy.com/dev_test_front_en/results/test_" . $Tt['id'] . "_5.js'>View</a><br/>";
			
			$Total = $Tt['Submit5'] - $Tt['Start'];
			
			echo "<br/>Duration: " . gmdate("H:i:s", $Total) . "<br/>";
		}
	}
	
	
	
	echo "<br/><br/><br/>";
	
	$sql = "SELECT * FROM dev_test_front ORDER BY id ASC";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($T = $db->fetch_array($query)){
			echo "<a href='http://reports.vidoomy.com/updates/tests_front.php?n=" . str_replace('L', '', $T['Last']) . "'>" . $T['Name'] . "</a> ";
			if($T['Submit1'] > 0){
				echo "<span style='color:green;'>Done</span><br/>";
			}else{
				echo "<span style='color:red;'>No</span><br/>";
			}
		}
	}
	
	
	echo "</div>";