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
	
	//echo $argv[1];
	$MonthS[1] = 'Enero';
	$MonthS[2] = 'Febrero';
	$MonthS[3] = 'Marzo';
	$MonthS[4] = 'Abril';
	$MonthS[5] = 'Mayo';
	$MonthS[6] = 'Junio';
	$MonthS[7] = 'Julio';
	$MonthS[8] = 'Agosto';
	$MonthS[9] = 'Septiembre';
	$MonthS[10] = 'Octubre';
	$MonthS[11] = 'Noviembre';
	$MonthS[12] = 'Diciembre';
	
	$MonthNice1 = '';
	$MonthNice2 = '';
	
	if(isset($argv[1])){
		if($argv[1] == 'semanal'){
			$TPL = 'seguimiento_semanal.html';
			$RepType = 'Semanal';
			
			$date1 = new DateTime();
			$date1->add(DateInterval::createFromDateString('yesterday'));
			$Date1 = $date1->format('Y-m-d');
			$Date1Nice = $date1->format('d/m/Y');
			
			$date2 = new DateTime($Date1);
			$date2->modify('-6 days');
			$Date2 = $date2->format('Y-m-d');
			$Date2Nice = $date2->format('d/m/Y');
			
			$date3 = new DateTime($Date1);
			$date3->modify('-1 week');
			$Date3 = $date3->format('Y-m-d');
			$Date3Nice = $date3->format('d/m/Y');
			
			$date4 = new DateTime($Date3);
			$date4->modify('-6 days');
			$Date4 = $date4->format('Y-m-d');
			$Date4Nice = $date4->format('d/m/Y');
		}elseif($argv[1] == 'quincenal'){
			$TPL = 'seguimiento_quincenal.html';
			$RepType = 'Quincenal';
			
			$date1 = new DateTime();
			$date1->add(DateInterval::createFromDateString('yesterday'));
			$Date1 = $date1->format('Y-m-d');
			$Date1Nice = $date1->format('d/m/Y');
			
			if(intval($date1->format('d')) > 20){
				$Date2 = $date1->format('Y-m-') . '16';
				$Date2Nice = '16' . $date1->format('/m/Y');
				
				$Date3 = $date1->format('Y-m-') . '15';
				$Date3Nice = '15' . $date1->format('/m/Y');
				
				$Date4 = $date1->format('Y-m-') . '01';
				$Date4Nice = '01' . $date1->format('/m/Y');
			}else{
				$Date2 = $date1->format('Y-m-') . '01';
				$Date2Nice = '01' . $date1->format('/m/Y');
				
				$dateLM = new DateTime($Date1);
				$dateLM->modify('-1 month');
				$Date3 = $dateLM->format('Y-m-t');
				$Date3Nice = $dateLM->format('t/m/Y');
				
				$Date4 = $dateLM->format('Y-m-') . '16';
				$Date4Nice = '16' . $dateLM->format('/m/Y');
			}
		
		}elseif($argv[1] == 'quincenal-anual'){
			$TPL = 'seguimiento_me_quincenal.html';
			$RepType = 'Quincenal - Anual';

			$date1 = new DateTime();
			$date1->add(DateInterval::createFromDateString('yesterday'));
			$Date1 = $date1->format('Y-m-d');
			$Date1Nice = $date1->format('d/m/Y');

			$dateLY = new DateTime($Date1);
			$dateLY->modify('-1 year');

			if(intval($date1->format('d')) > 20){
				$Date2 = $date1->format('Y-m-16');
				$Date2Nice = $date1->format('16/m/Y');

				$Date3 = $dateLY->format('Y-m-d');
				$Date3Nice = $dateLY->format('t/m/Y');

				$Date4 = $dateLY->format('Y-m-16');
				$Date4Nice = $dateLY->format('16/m/Y');
			}else{
				$Date2 = $date1->format('Y-m-01');
				$Date2Nice = $date1->format('01/m/Y');

				$Date3 = $dateLY->format('Y-m-t');
				$Date3Nice = $dateLY->format('t/m/Y');

				$Date4 = $dateLY->format('Y-m-01');
				$Date4Nice = $dateLY->format('01/m/Y');
			}

			$MonthNice1 = "Última Quincena";
			$MonthNice2 = "Año Anterior";
	}elseif($argv[1] == 'mensual'){
			$TPL = 'seguimiento_mensual.html';
			$RepType = 'Mensual';
			
			$date1 = new DateTime();
			$date1->add(DateInterval::createFromDateString('yesterday'));
			$Date1 = $date1->format('Y-m-d');
			$Date1Nice = $date1->format('d/m/Y');
			
			$Date2 = $date1->format('Y-m-') . '01';
			$Date2Nice = '01' . $date1->format('/m/Y');
			
			$date3 = new DateTime($Date2);
			$date3->modify('-1 month');

			$Date3 = $date3->format('Y-m-t');
			$Date3Nice = $date3->format('t/m/Y');
			
			$Date4 = $date3->format('Y-m-') . '01';
			$Date4Nice = '01' . $date3->format('/m/Y');
			
			$KeyM1 = intval($date1->format('n'));
			$MonthNice1 = $MonthS[$KeyM1];
			$KeyM2 = intval($date3->format('n'));
			$MonthNice2 = $MonthS[$KeyM2];
		}else{
			die('Invalid time range');
		}
	}else{
		die('No time range');
	}
	
	
	echo "Date 1: $Date1 \n Date 2: $Date2 \n Date 3: $Date3 \n Date 4: $Date4 \n ";
	//exit(0);
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

function getList($Date1, $Date2, $idAccM, $FlCheck = false){
	global $db;
	
	$Data = array();

	$arD1 = explode('-', $Date1);
	$Table1 = 'reports_resume' . $arD1[0] . $arD1[1];
	
	$arD2 = explode('-', $Date2);
	$Table2 = 'reports_resume' . $arD2[0] . $arD2[1];
	
	if($Table1 == $Table2){
		$sql = "SELECT 
			$Table1.Domain AS DomainID,
			reports_domain_names.Name AS Domain,
		    users.user AS Username,
		    users.nick AS Nick,
			SUM($Table1.formatLoads) AS FL,
		    concat('$',FORMAT(SUM($Table1.Revenue),2)) AS Revenue
			
		FROM `$Table1`
		
		INNER JOIN reports_domain_names ON reports_domain_names.id = $Table1.Domain
		INNER JOIN users ON users.id = $Table1.idUser
		INNER JOIN acc_managers ON acc_managers.id = users.AccM
		
		WHERE 
			$Table1.Date BETWEEN '$Date2' AND '$Date1' 
		AND	
			users.AccM = $idAccM
		AND 
			$Table1.Domain != 386
		    
		GROUP BY Domain
		
		ORDER BY FL DESC";
	}else{
		$sql = "SELECT
			DomainID,
			Domain,
			Username,
			Nick,
			SUM(FL) AS FL,
			concat('$',FORMAT(SUM(Revenue),2)) AS Revenue
		FROM
		
		((SELECT 
					$Table2.Domain AS DomainID,
					reports_domain_names.Name AS Domain,
				    users.user AS Username,
				    users.nick AS Nick,
					SUM($Table2.formatLoads) AS FL,
				   	SUM($Table2.Revenue) AS Revenue
					
				FROM $Table2
				
				INNER JOIN reports_domain_names ON reports_domain_names.id = $Table2.Domain
				INNER JOIN users ON users.id = $Table2.idUser
				INNER JOIN acc_managers ON acc_managers.id = users.AccM
				
				WHERE 
					$Table2.Date BETWEEN '$Date2' AND '$Date1'
				AND	
					users.AccM = $idAccM
				AND 
					$Table2.Domain != 386
		  
		  		GROUP BY Domain
		)
		
		UNION ALL
		
		(SELECT 
					$Table1.Domain AS DomainID,
					reports_domain_names.Name AS Domain,
				    users.user AS Username,
				    users.nick AS Nick,
					SUM($Table1.formatLoads) AS FL,
				    SUM($Table1.Revenue) AS Revenue
					
				FROM $Table1
				
				INNER JOIN reports_domain_names ON reports_domain_names.id = $Table1.Domain
				INNER JOIN users ON users.id = $Table1.idUser
				INNER JOIN acc_managers ON acc_managers.id = users.AccM
				
				WHERE 
					$Table1.Date BETWEEN '$Date2' AND '$Date1'
				AND	
					users.AccM = $idAccM
				AND 
					$Table1.Domain != 386
		 
		 		GROUP BY Domain
		)) AS R
		
		GROUP BY Domain
				
		ORDER BY FL DESC";
	}
	
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

function getGlobal($Date1, $Date2, $idAccM){
	global $db;
	
	$Data = array();

	$arD1 = explode('-', $Date1);
	$Table1 = 'reports_resume' . $arD1[0] . $arD1[1];
	
	$arD2 = explode('-', $Date2);
	$Table2 = 'reports_resume' . $arD2[0] . $arD2[1];
	
	if($Table1 == $Table2){	
		$sql = "SELECT concat('$',FORMAT(SUM($Table1.Revenue),2)) AS Revenue, SUM($Table1.formatLoads) AS FL		
		FROM `$Table1`
		INNER JOIN users ON users.id = $Table1.idUser
		INNER JOIN acc_managers ON acc_managers.id = users.AccM
		
		WHERE 
			$Table1.Date BETWEEN '$Date2' AND '$Date1' 
		AND	
			users.AccM = $idAccM";
	}else{
		$sql = "SELECT
			SUM(FL) AS FL,
			concat('$',FORMAT(SUM(Revenue),2)) AS Revenue
		FROM
		
		((SELECT SUM($Table2.Revenue) AS Revenue, SUM($Table2.formatLoads) AS FL		
				FROM $Table2
				INNER JOIN users ON users.id = $Table2.idUser
				INNER JOIN acc_managers ON acc_managers.id = users.AccM
				
				WHERE 
					$Table2.Date BETWEEN '$Date2' AND '$Date1' 
				AND	
					users.AccM = $idAccM
		)
		
		UNION ALL
		
		(SELECT SUM($Table1.Revenue) AS Revenue, SUM($Table1.formatLoads) AS FL		
				FROM $Table1
				INNER JOIN users ON users.id = $Table1.idUser
				INNER JOIN acc_managers ON acc_managers.id = users.AccM
				
				WHERE 
					$Table1.Date BETWEEN '$Date2' AND '$Date1' 
				AND	
					users.AccM = $idAccM
		)) AS R";
	}



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
	
	$sql = "SELECT * FROM acc_managers WHERE Follow = 1 AND Deleted = 0"; // AND id = 6
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
				
				$List = getList($Date1, $Date2, $idAccM);
				$ListY = getList($Date3, $Date4, $idAccM, true);
				
				foreach($ListY as $idDom => $DomData){
					
					$FormatsAntesAyer = intval($DomData['Formats']);

					if(array_key_exists($idDom, $List)){
						$FormatsAyer = intval($List[$idDom]['Formats']);
					}else{
						$FormatsAyer = 0;
					}
					
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
				
				
				$List2 = getList($Date1, $Date2, $idAccM, true);
				$ListY2 = getList($Date3, $Date4, $idAccM);
				
				foreach($List2 as $idDom => $DomData){
					$FormatsAyer = intval($DomData['Formats']);
					
					if(array_key_exists($idDom, $ListY2)){
						$FormatsAntesAyer = intval($ListY2[$idDom]['Formats']);
					}else{
						$FormatsAntesAyer = 0;
					}
						
					if($FormatsAyer > $FormatsAntesAyer){
						//echo "Mayor";
						
						$Dif = $FormatsAyer - $FormatsAntesAyer;
						
						if($FormatsAntesAyer > 0){
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
				
				//echo '<br/>';
				
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
				$mail->Username = "notify@vidoomy.net";
				$mail->Password = "24NotTFiY-9G8";//NosdFiY-98
				$mail->CharSet = 'UTF-8';
				$mail->setFrom('notify@vidoomy.net', 'Vidoomy');
				$mail->addReplyTo('notify@vidoomy.net', 'Vidoomy');
								
				$mail->addAddress($UserEmail, $UserName);
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
					    
					    if(array_key_exists($idDom, $List)){
						    $DOMFL = number_format($List[$idDom]['Formats'], 0, '', '.');
						    $DOMREV = $List[$idDom]['Revenue'];
						}else{
							$DOMFL = 0;
							$DOMREV = '$0.00';
						}
					    
					    $Row = '<tr style="background-color: ' . $BGColor . ';">';
					    
					    $Row .= '<td style="font-family: sans-serif; color:red;">' . $ListY[$idDom]['Domain'] . "</td>";
					    $Row .= '<td style="font-family: sans-serif; color:red;">' . $ListY[$idDom]['Partner'] . "</td>";
					    $Row .= '<td style="font-family: sans-serif; color:red;">' . number_format($ListY[$idDom]['Formats'], 0, '', '.') . "</td>";
					    $Row .= '<td style="font-family: sans-serif; color:red;">' . $DOMFL . "</td>";
					    $Row .= '<td style="font-family: sans-serif; color:red;">' . "-$VarPorc%" . "</td>";
					    $Row .= '<td style="font-family: sans-serif; color:red;" class="hidem">' . $ListY[$idDom]['Revenue'] . "</td>";
					    $Row .= '<td style="font-family: sans-serif; color:red;" class="hidem">' . $DOMREV . "</td>";
					    
					    $Row .= "</tr>";
					    
					    $Rows .= $Row;
					    
					    $Nc++;
					}
				}
				
				if(count($Increasing) > 0){
				    foreach($Increasing as $idDom => $VarPorc){
					    $VarPorc = number_format($VarPorc, 2, ',', '');
					    if(!array_key_exists($idDom, $ListY2)) {
						    $ListY2[$idDom]['Formats'] = 0;
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
				
				$Global = getGlobal($Date1, $Date2, $idAccM);
				$GlobalY = getGlobal($Date3, $Date4, $idAccM);
	
				$MailContent = file_get_contents('/var/www/html/login/emailstpl/' . $TPL);
				$MailContent = str_replace('#Rows#', $Rows, $MailContent);
				$MailContent = str_replace('#Name#', $Name, $MailContent);
				$MailContent = str_replace('#Date1#', $Date1Nice, $MailContent);
				$MailContent = str_replace('#Date2#', $Date2Nice, $MailContent);
				$MailContent = str_replace('#Date3#', $Date3Nice, $MailContent);
				$MailContent = str_replace('#Date4#', $Date4Nice, $MailContent);
				
				$MailContent = str_replace('#NiceMonth1#', $MonthNice1, $MailContent);
				$MailContent = str_replace('#NiceMonth2#', $MonthNice2, $MailContent);
				
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
					$RowSummary = '<tr style="background-color: #fcfcfc;"><td style="font-family: sans-serif; color:black; text-align:center;" colspan="5">No hay ningún publisher activo.</td></tr>';
					$HidemS = '';
				}	
				
				$MailContent = str_replace('#RowSummary#', $RowSummary, $MailContent);
				$MailContent = str_replace('#SColor#', $SColor, $MailContent);
				$MailContent = str_replace('#HidemS#', $HidemS, $MailContent);
				$MailContent = str_replace('#Hidem#', $Hidem, $MailContent);
				
				//$mail->Subject = 'Reporte de variaciones para ' . $Name;
				$mail->Subject = "Reporte de variaciones ($RepType) $Name";
				$mail->msgHTML($MailContent);
				$mail->send();
				
				//exit(0);
			}
			//sleep(40);
		}
	}