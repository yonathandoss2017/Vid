<?php
function eliminar_tildes($cadena){
 
    //Codificamos la cadena en formato utf8 en caso de que nos de errores
   // $cadena = utf8_encode($cadena);
 
    //Ahora reemplazamos las letras
    $cadena = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
        $cadena
    );
 
    $cadena = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $cadena );
 
    $cadena = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $cadena );
 
    $cadena = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        $cadena );
 
    $cadena = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $cadena );
 
    $cadena = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç'),
        array('n', 'N', 'c', 'C'),
        $cadena
    );
 
    return $cadena;
}
	//echo 'A';
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	
	//echo 1;
	
	require('config.php');
	require('constantes.php');
	require('db.php');
	require('common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	//echo $milliseconds = round(microtime(true) * 1000) . '<br />';
	require('../Vidoomy/ads/MaxMind-DB-Reader-php-master/autoload.php');
	//echo 2;
	use MaxMind\Db\Reader;
	$reader = new Reader('../Vidoomy/ads/MaxMind/GeoIP2-City.mmdb');
	//$reader = new Reader('MaxMind/GeoIP2-Country.mmdb');
	
	//$ipAddress = $_SERVER['REMOTE_ADDR'];
	$ipAddress = '24.24.24.20';
	//$databaseFile = 'GeoIP2-City.mmdb';

	//$reader = new Reader($databaseFile);
	
	//echo $milliseconds = round(microtime(true) * 1000);
	
	$Data = $reader->get($ipAddress);
	
	//print_r($Data);
	
	$Continent = $Data['continent']['code'];
	$Country = $Data['country']['iso_code'];
	
	echo strtolower( eliminar_tildes( $Data['city']['names']['en'] ) );
	
	worldcitiespop.txt
	
	
	//echo '<br />' . $milliseconds = round(microtime(true) * 1000);
?>