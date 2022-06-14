<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/common.lib.php');
	
	require '/var/www/html/site/include/PHPMailer/PHPMailerAutoload.php';
	
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$PastYesterday = date('Y-m-d', time() - 48 * 3600);
	$Yesterday = date('Y-m-d', time() - 24 * 3600);
	$PlainToday = date('Ymd');
	
	$AntesAyerNice = date('d/m/Y', time() - 48 * 3600);
	$AyerNice = date('d/m/Y', time() - 24 * 3600);
	$HoyNice = date('d/m/Y', time());

function checkActivePubs($idAccM){
	global $db;
	
	
	$sql = "SELECT COUNT(*) FROM users 
	WHERE users.AccM = $idAccM";
	
	if($db->getOne($sql) > 0){
		return true;
	}else{
		return false;
	}
}

function getList($Date, $idAccM, $FlCheck = false){
	global $db;
	
	$arD = explode('-', $Date);
	$Data = array();
	$Table = 'reports_resume' . $arD[0] . $arD[1];
	
	$sql = "SELECT 
		$Table.Domain AS DomainID,
		reports_domain_names.Name AS Domain,
	    users.user AS Username,
	    users.nick AS Nick,
		SUM($Table.formatLoads) AS FL,
	    concat('$',FORMAT(SUM($Table.Revenue),2)) AS Revenue
		
	FROM `$Table`
	
	INNER JOIN reports_domain_names ON reports_domain_names.id = $Table.Domain
	INNER JOIN users ON users.id = $Table.idUser
	INNER JOIN acc_managers ON acc_managers.id = users.AccM
	
	WHERE 
		$Table.Date = '$Date'
	AND	
		users.AccM = $idAccM
	    
	GROUP BY Domain
	
	ORDER BY FL DESC";
	
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($List = $db->fetch_array($query)){
			if($List['FL'] >= 10000 || $FlCheck === false){
				
				if($List['Nick'] != ''){
					$Partner = $List['Nick'];
				}else{
					$Partner = $List['Username'];
				}
				
				$D = array(
					'Domain'	=>	$List['Domain'],
					'Partner'	=>	$Partner,
					'Formats'	=>	intval($List['FL']),
					'Revenue'	=>	$List['Revenue']
				);
				
				$Data[$List['DomainID']] = $D;
				
			}
		}
	}
	
	return $Data;
}

function getGlobal($Date, $idAccM){
	global $db;
	
	$arD = explode('-', $Date);
	$Data = array();
	$Table = 'reports_resume' . $arD[0] . $arD[1];
	
	$sql = "SELECT concat('$',FORMAT(SUM($Table.Revenue),2)) AS Revenue, SUM($Table.formatLoads) AS FL		
	FROM `$Table`
	INNER JOIN users ON users.id = $Table.idUser
	INNER JOIN acc_managers ON acc_managers.id = users.AccM
	
	WHERE 
		$Table.Date = '$Date'
	AND	
		users.AccM = $idAccM";
	//exit(0);		
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		$DataQ = $db->fetch_array($query);
		if($DataQ['FL'] === NULL){
			return false;
		}else{
			$Data['Revenue'] = $DataQ['Revenue'];
			$Data['FL'] = $DataQ['FL'];
			
			return $Data;
		}
	}else{
		return false;
	}
}
	
	$sql = "SELECT * FROM acc_managers WHERE Follow = 1 AND Deleted = 0";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($AccM = $db->fetch_array($query)){
			$idAccM = $AccM['id'];
			$Email = $AccM['Email'];
			$AccMName = str_replace(' ','_',$AccM['Name']);
			$Name = $AccM['Name'];
			
			if(checkActivePubs($idAccM)){
				//echo $Email . ' - ' . $Name . "<br/>";
				
				$Increasing = array();
				$Decrising = array();
				
				$List = getList($Yesterday, $idAccM);
				$ListY = getList($PastYesterday, $idAccM, true);
				
				foreach($ListY as $idDom => $DomData){
					
					$FormatsAntesAyer = intval($DomData['Formats']);
					$FormatsAyer = intval($List[$idDom]['Formats']);
					
					//echo $DomData['Domain'] . ' ' . $FormatsAntesAyer . ' - ' . $FormatsAyer . ': ';
					
					if($FormatsAntesAyer > $FormatsAyer){
						//echo "Menor ";
						
						$Dif = $FormatsAntesAyer - $FormatsAyer;
						
						if($FormatsAntesAyer > 0){
							$Porc = $Dif / $FormatsAntesAyer * 100;
						}else{
							$Porc = 100;
						}
						
						if($Porc >= 40){
							/*
							echo '<span style="color:red">';
							echo $Porc . '%';
							echo '</span>';
							*/
							$Decrising[$idDom] = $Porc;
							
						}else{
							/*
							echo '<span style="color:green">';
							echo $Porc . '%';
							echo '</span>';
							*/
						}
					}
				}
				
				
				$List2 = getList($Yesterday, $idAccM, true);
				$ListY2 = getList($PastYesterday, $idAccM);
				
				foreach($List2 as $idDom => $DomData){
					$FormatsAyer = $DomData['Formats'];
					$FormatsAntesAyer = $ListY2[$idDom]['Formats'];
					
					if($FormatsAyer > $FormatsAntesAyer){
						//echo "Mayor";
						
						$Dif = $FormatsAyer - $FormatsAntesAyer;
						
						if($FormatsAntesAyer > 0){
							//$Porc = $Dif / $FormatsAyer * 100;
							$Porc = $Dif / $FormatsAntesAyer * 100;
						}else{
							$Porc = 100;
						}
						
						if($Porc >= 50){
							/*
							echo '<span style="color:blue">';
							echo $Porc . '%';
							echo '</span>';
							*/
							$Increasing[$idDom] = $Porc;
							
						}else{
							/*
							echo '<span style="color:yellow">';
							echo $Porc . '%';
							echo '</span>';
							*/
						}
						
					}else{
						//echo "Igual";
					}
				}
				
				echo '<br/>';
				
				$UserEmail = $Email; //"eric.raventos@vidoomy.com";
				//$UserEmail = 'eric.raventos@vidoomy.com';
				//$UserEmail = 'federico.izuel@vidoomy.com';
				//$UserEmail = 'email'.$idAccM.'@vidoom.com';
				$UserName = $Name;
				
				$mail = new PHPMailer;
				
				$mail->isSMTP();
				$mail->SMTPDebug = 0;
				$mail->Debugoutput = 'html';
				
				$mail->Host = 'smtp.gmail.com';
				$mail->Port = 465;
				$mail->SMTPSecure = 'ssl';
				$mail->SMTPAuth = true;
				$mail->Username = $emailing2Cred['user'];
				$mail->Password = $emailing2Cred['password'];
				$mail->CharSet = 'UTF-8';
				$mail->setFrom($emailing2Cred['user'], 'Vidoomy');
				$mail->addReplyTo($emailing2Cred['user'], 'Vidoomy');
				$mail->addAddress($UserEmail, $UserName);
				
				//$mail->AddCC('marcos.cuesta@vidoomy.com');
				$mail->AddBCC('federico.izuel@vidoomy.com');
				
				$Rows = "";
				$BGColor1 = "#FCFCFC";
				$BGColor2 = "#EEEEEE";
				$Nc = 0;
				
				$HidemS = 'class="hidem"';
				$Hidem = 'class="hidem"';
				
				if(count($Decrising) > 0){
				    foreach($Decrising as $idDom => $VarPorc){
					    $VarPorc = number_format($VarPorc, 2, ',', '');
					    
					    if($Nc % 2 == 0){
						    $BGColor = $BGColor1;
					    }else{
						    $BGColor = $BGColor2;
					    }
					    
					    $Row = '<tr style="background-color: ' . $BGColor . ';">';
					    
					    $Row .= '<td style="font-family: sans-serif; color:red;">' . $ListY[$idDom]['Domain'] . "</td>";
					    $Row .= '<td style="font-family: sans-serif; color:red;">' . $ListY[$idDom]['Partner'] . "</td>";
					    $Row .= '<td style="font-family: sans-serif; color:red;">' . number_format($ListY[$idDom]['Formats'], 0, '', '.') . "</td>";
					    $Row .= '<td style="font-family: sans-serif; color:red;">' . number_format($List[$idDom]['Formats'], 0, '', '.') . "</td>";
					    $Row .= '<td style="font-family: sans-serif; color:red;">' . "-$VarPorc%" . "</td>";
					    $Row .= '<td style="font-family: sans-serif; color:red;" class="hidem">' . $ListY[$idDom]['Revenue'] . "</td>";
					    $Row .= '<td style="font-family: sans-serif; color:red;" class="hidem">' . $List[$idDom]['Revenue'] . "</td>";
					    
					    $Row .= "</tr>";
					    
					    $Rows .= $Row;
					    
					    $Nc++;
					}
				}
				
				if(count($Increasing) > 0){
				    foreach($Increasing as $idDom => $VarPorc){
					    $VarPorc = number_format($VarPorc, 2, ',', '');
					    if(intval($ListY2[$idDom]['Formats']) == 0){
						    $ListY2[$idDom]['Formats'] = 0;
					    }
					    if(floatval(str_replace('$', '', $ListY2[$idDom]['Revenue'])) == 0){
						    $ListY2[$idDom]['Revenue'] = '$0,00';
					    }
					    
					    if($Nc % 2 == 0){
						    $BGColor = $BGColor1;
					    }else{
						    $BGColor = $BGColor2;
					    }
					    
					    $Row = '<tr style="background-color: ' . $BGColor . ';">';
					    
					    $Row .= '<td style="font-family: sans-serif; color:green;">' . $List2[$idDom]['Domain'] . "</td>";
					    $Row .= '<td style="font-family: sans-serif; color:green;">' . $List2[$idDom]['Partner'] . "</td>";
					    $Row .= '<td style="font-family: sans-serif; color:green;">' . number_format($ListY2[$idDom]['Formats'], 0, '', '.') . "</td>";
					    $Row .= '<td style="font-family: sans-serif; color:green;">' . number_format($List2[$idDom]['Formats'], 0, '', '.') . "</td>";
					    $Row .= '<td style="font-family: sans-serif; color:green;">' . "$VarPorc%" . "</td>";
					    $Row .= '<td style="font-family: sans-serif; color:green;" class="hidem">' . $ListY2[$idDom]['Revenue'] . "</td>";
					    $Row .= '<td style="font-family: sans-serif; color:green;" class="hidem">' . $List2[$idDom]['Revenue'] . "</td>";
					    
					    $Row .= "</tr>";
					    
				        $Rows .= $Row;
				        
				        $Nc++;	    
				    }
				}
				
				if($Nc == 0){
					$Row = '<tr style="background-color: ' . $BGColor . ';">';
				    $Row .= '<td style="font-family: sans-serif; color:black; text-align:center;" colspan="7">No hay variaciones destacadas.</td>';
				    $Row .= "</tr>";
				    
			        $Rows .= $Row;
			        
			        $Hidem = '';
				}
				
				$Global = getGlobal($Yesterday, $idAccM);
				$GlobalY = getGlobal($PastYesterday, $idAccM);
	
				$MailContent = file_get_contents('/var/www/html/login/emailstpl/seguimiento.html');
				$MailContent = str_replace('#Rows#', $Rows, $MailContent);
				$MailContent = str_replace('#Name#', $Name, $MailContent);
				$MailContent = str_replace('#Date1#', $AyerNice, $MailContent);
				$MailContent = str_replace('#Date2#', $AntesAyerNice, $MailContent);
				$MailContent = str_replace('#Date#', $HoyNice, $MailContent);
				
				$SColor = 'black';
				
				if($Global !== false){
					if($GlobalY === false){
						$GlobalY['FL'] = 0;
						$GlobalY['Revenue'] = 0;
					}
					
					if($Global['FL'] > $GlobalY['FL']){
						$SColor = 'green';
						
						$Dif = $Global['FL'] - $GlobalY['FL'];
						
						if($GlobalY['FL'] > 0){
							$Porc = $Dif / $GlobalY['FL'] * 100;
						}else{
							$Porc = 100;
						}
						
						$Sig = '+';
					}elseif($Global['FL'] < $GlobalY['FL']){
						$SColor = 'red';
						
						$Dif = $GlobalY['FL'] - $Global['FL'];
						
						if($GlobalY['FL'] > 0){
							$Porc = $Dif / $GlobalY['FL'] * 100;
						}else{
							$Porc = 100;
						}
						$Sig = '-';
					}else{
						$Porc = 0;					
						$Sig = '';
					}
					
					$RowSummary = file_get_contents('/var/www/html/login/emailstpl/seguimiento_resumen_row.html');
					$RowSummary = str_replace('#FLAA#', number_format($GlobalY['FL'], 0, '', '.'), $RowSummary);
					$RowSummary = str_replace('#FLA#', number_format($Global['FL'], 0, '', '.'), $RowSummary);
					$RowSummary = str_replace('#FLV#', $Sig . number_format($Porc, 2, ',', '.') . '%', $RowSummary);
					
					$RowSummary = str_replace('#RevA#', $Global['Revenue'], $RowSummary);
					$RowSummary = str_replace('#RevAA#', $GlobalY['Revenue'], $RowSummary);
				}else{
					/*
					$MailContent = str_replace('#FLAA#', '0', $MailContent);
					$MailContent = str_replace('#FLA#', '0', $MailContent);
					$MailContent = str_replace('#FLV#', '0,00%', $MailContent);
					
					$MailContent = str_replace('#RevA#', '$0,00', $MailContent);
					$MailContent = str_replace('#RevAA#', '$0,00', $MailContent);
					*/
					$RowSummary = '<tr style="background-color: #fcfcfc;"><td style="font-family: sans-serif; color:black; text-align:center;" colspan="5">No hay ningún publisher activo.</td></tr>';
					$HidemS = '';
				}	
				
				$MailContent = str_replace('#RowSummary#', $RowSummary, $MailContent);
				$MailContent = str_replace('#SColor#', $SColor, $MailContent);
				$MailContent = str_replace('#HidemS#', $HidemS, $MailContent);
				$MailContent = str_replace('#Hidem#', $Hidem, $MailContent);
				
				//$mail->Subject = 'Reporte de variaciones para ' . $Name;
				$mail->Subject = "Reporte de variaciones $Name $HoyNice";
				$mail->msgHTML($MailContent);
				$mail->send();
				
				//exit(0);
			}
			sleep(40);
		}
	}