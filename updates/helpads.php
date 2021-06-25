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
	require('../admin/libs/display.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);

function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}
	$Sliders = true;
	$Intexts = true;
	$Do = false;
	
	if(isset($_GET['do'])){
		if($_GET['do'] == 1){
			$Do = true;
		}
	}
	
	if(isset($_GET['s'])){
		if($_GET['s'] == 1){
			$Intexts = false;
		}
	}
	
	if(isset($_GET['i'])){
		if($_GET['i'] == 1){
			$Sliders = false;
		}
	}
	
	$idS = intval($_GET['id']);
	
	$sql = "SELECT * FROM ads WHERE idSite = $idS LIMIT 6"; //>= 63 AND id <= 99 
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Ad = $db->fetch_array($query)){
			
			$Time = time();
			$Date = date('Y-m-d');
			
			$FileContent = $Ad['CCode'];
			$idSite = $Ad['idSite'];
			$idAd = $Ad['id'];
			echo "$idAd ($idSite)";
			/*
			$PlayerD['Width'] = intval(get_string_between($FileContent, "playerWidth:", ","));
			$PlayerD['Height'] = intval(get_string_between($FileContent, "playerHeight:", ","));
			$PlayerD['Position'] = trim(get_string_between($FileContent, "slidePosition: '", "',"));
			$PlayerD['Sid'] = trim(get_string_between($FileContent, "sid: ", ","));
			*/
			/*
			
			if(strpos($FileContent, "// Slider Desktop") !== false){
				echo 'SI';
			}else{
				echo 'NO';
			}
			echo ' - ';
			if(strpos($FileContent, "// SLider MW") !== false){
				echo 'SI';
			}else{
				echo 'NO';
			}
			echo ' - ';
			if(strpos($FileContent, "// Intext Desktop") !== false){
				echo 'SI';
			}else{
				echo 'NO';
			}
			echo ' - ';
			if(strpos($FileContent, "// Intext MW") !== false){
				echo 'SI';
			}else{
				echo 'NO';
			}
			echo '<br/>';
			*/
			
			
			if($Sliders){	
				//Slider Desktop
				$AdCode = get_string_between($FileContent,'//slider dt','})();');
				if($AdCode == ''){
					$AdCode = get_string_between($FileContent,'// slider dt','})();');
					if($AdCode == ''){
						$AdCode = get_string_between($FileContent,'// slider desktop','})();');
						if($AdCode == ''){
							$AdCode = get_string_between($FileContent,'//slider_dt','})();');
							if($AdCode == ''){
								$AdCode = get_string_between($FileContent,'// Slider Desktop','})();');
							}
						}
					}
				}
				
				$Width = intval(get_string_between($AdCode, "playerWidth:", ","));
				$Height = intval(get_string_between($AdCode, "playerHeight:", ","));
				$Sid = trim(get_string_between($AdCode, "sid: ", ","));
				$DFP = trim(get_string_between($AdCode, "dfp: ", ","));
				if($DFP == 'true'){ $DFP = 1; } else { $DFP = 2; }
				$Position = trim(get_string_between($AdCode, "slidePosition: '", "',"));
				if($Position == 'left'){$Pos = 2;} else {$Pos = 1;}
				
				echo '<br/>Slider Desktop: ' . $Width . 'x' . $Height . ' SID:' . $Sid . ' ' . $DFP . ' ' . $Position;
				$sql = "INSERT INTO ads (idSite, idLKQD, divID, Type, Width, Height, Close, DFP, Override, HeightA, SPosition, CCode, Time, Date) VALUES ('$idSite', '$Sid', '', 3, '$Width', '$Height', 1, '$DFP', 2, 0, '$Pos', '', '$Time', '$Date')";
				if($Do){
					$db->query($sql);
				}
				
				//SLider MW
				$AdCode = get_string_between($FileContent,'//slider mw','})();');
				if($AdCode == ''){
					$AdCode = get_string_between($FileContent,'// slider mw','})();');	
					if($AdCode == ''){
						$AdCode = get_string_between($FileContent,'//slider MW','})();');
						if($AdCode == ''){
							$AdCode = get_string_between($FileContent,'//slider_mw','})();');
							if($AdCode == ''){
								$AdCode = get_string_between($FileContent,'// SLider MW','})();');	
							}
						}
					}
				}
				
				$Width = intval(get_string_between($AdCode, "playerWidth:", ","));
				$Height = intval(get_string_between($AdCode, "playerHeight:", ","));
				$Sid = trim(get_string_between($AdCode, "sid: ", ","));
				$DFP = trim(get_string_between($AdCode, "dfp: ", ","));
				if($DFP == 'true'){ $DFP = 1; } else { $DFP = 2; }
				$Position = trim(get_string_between($AdCode, "slidePosition: '", "',"));
				if($Position == 'left'){$Pos = 2;} else {$Pos = 1;}
				
				echo '<br/>Slider MW: ' . $Width . 'x' . $Height . ' SID:' . $Sid . ' ' . $DFP . ' ' . $Position;
				$sql = "INSERT INTO ads (idSite, idLKQD, divID, Type, Width, Height, Close, DFP, Override, HeightA, SPosition, CCode, Time, Date) VALUES ('$idSite', '$Sid', '', 4, '$Width', '$Height', 1, '$DFP', 2, 0, '$Pos', '', '$Time', '$Date')";
				if($Do){
					$db->query($sql);
				}
			
			}
			
			if($Intexts){
				
				//Intext Desktop
				$AdCode = get_string_between($FileContent,'//intext dt','})();');
				if($AdCode == ''){
					$AdCode = get_string_between($FileContent,'// intext dt','})();');	
					if($AdCode == ''){
						$AdCode = get_string_between($FileContent,'// desktop intext','})();');
						if($AdCode == ''){
							$AdCode = get_string_between($FileContent,'//intext_dt','})();');
							if($AdCode == ''){
								$AdCode = get_string_between($FileContent,'// Intext Desktop','})();');
								if($AdCode == ''){
									$AdCode = get_string_between($FileContent,'//incontent dt','})();');
									if($AdCode == ''){
										$AdCode = get_string_between($FileContent,'// intext_dt','})();');
										if($AdCode == ''){
											$AdCode = get_string_between($FileContent,'// inread dt','})();');
										}
									}
								}
								
							}
						}
					}
				}
				
				$ContID = '';
				$Width = intval(get_string_between($AdCode, "playerWidth:", ","));
				$Height = intval(get_string_between($AdCode, "playerHeight:", ","));
				$Sid = trim(get_string_between($AdCode, "sid: ", ","));
				$DFP = trim(get_string_between($AdCode, "dfp: ", ","));
				$ContID = trim(get_string_between($AdCode, "playerContainerId: '", "',"));
				if($DFP == 'true'){ $DFP = 1; } else { $DFP = 2; }
				
				if($Width == 0){
					$Width = intval(get_string_between($AdCode, "playerWidth: '", "',"));
					if($Width == 0){
						$Width = 640;
					}
				}
				if($Height == 0){
					$Height = intval(get_string_between($AdCode, "playerHeight: '", "',"));
					if($Height == 0){
						$Height = 360;
					}
				}
				
				echo '<br/>Intext Desktop: (' . $ContID . ') ' . $Width . 'x' . $Height . ' SID:' . $Sid . ' ' . $DFP;
				$sql = "INSERT INTO ads (idSite, idLKQD, divID, Type, Width, Height, Close, DFP, Override, HeightA, SPosition, CCode, Time, Date) VALUES ('$idSite', '$Sid', '$ContID', 1, '$Width', '$Height', 1, '$DFP', 2, 0, 1, '', '$Time', '$Date')"; 
				if($Do){
					$db->query($sql);
				}
				
				//Intext MW
				$AdCode = get_string_between($FileContent,'//intext mw','})();');
				if($AdCode == ''){
					$AdCode = get_string_between($FileContent,'// intext mw','})();');
					if($AdCode == ''){
						$AdCode = get_string_between($FileContent,'// mw intext','})();');
						if($AdCode == ''){
							$AdCode = get_string_between($FileContent,'//intext_mw','})();');
							if($AdCode == ''){
								$AdCode = get_string_between($FileContent,'// Intext MW','})();');
								if($AdCode == ''){
									$AdCode = get_string_between($FileContent,'//incontent mw','})();');
									if($AdCode == ''){
										$AdCode = get_string_between($FileContent,'// intext_mb','})();');
										if($AdCode == ''){
											$AdCode = get_string_between($FileContent,'// inread mw','})();');
										}
									}
								}
							}
						}
					}
				}
				
				$Width = intval(get_string_between($AdCode, "playerWidth:", ","));
				$Height = intval(get_string_between($AdCode, "playerHeight:", ","));
				$Sid = trim(get_string_between($AdCode, "sid: ", ","));
				$DFP = trim(get_string_between($AdCode, "dfp: ", ","));
				$ContID = trim(get_string_between($AdCode, "playerContainerId: '", "',"));
				if($DFP == 'true'){ $DFP = 1; } else { $DFP = 2; }
				
				if($Width == 0){
					$Width = intval(get_string_between($AdCode, "playerWidth: '", "',"));
					if($Width == 0){
						$Width = 400;
					}	
				}
				if($Height == 0){
					$Height = intval(get_string_between($AdCode, "playerHeight: '", "',"));
					if($Height == 0){
						$Height = 225;
					}
				}
				
				echo '<br/>Intext MW: (' . $ContID . ') ' . $Width . 'x' . $Height . ' SID:' . $Sid . ' ' . $DFP;
				
				$sql = "INSERT INTO ads (idSite, idLKQD, divID, Type, Width, Height, Close, DFP, Override, HeightA, SPosition, CCode, Time, Date) VALUES ('$idSite', '$Sid', '$ContID', 2, '$Width', '$Height', 1, '$DFP', 2, 0, 1, '', '$Time', '$Date')";
				if($Do){
					$db->query($sql);
				}
			
			}	
			
			
			if($Do){
				$sql = "DELETE FROM ads WHERE id = '$idAd' LIMIT 1";
				$db->query($sql);
				
				newGenerateJS($idSite);
				
				echo '<br/> DONE!';
			}
			
		}
	}
	
	
	
	
	