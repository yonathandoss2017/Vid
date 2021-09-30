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
	
	$db = new SQL($dbhost, 'vidoomy_adv', $dbuser, $dbpass);
	$db3 = new SQL($advProd["host"], $advProd["db"], $advProd["user"], $advProd["pass"]);
	
	//$argv[1] = $_GET['d'];
	
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
	
	$ListInc = array();
	$ListDec = array();
	
	$HidemS = 'class="hidem"';
	$Hidem = 'class="hidem"';
	
	if(isset($argv[1])){
		if($argv[1] == 'diario'){
			$TPL = 'seguimiento_adv_diario.html';
			$TPLSales = 'seguimiento_adv_diario_sales.html';
			$RepType = 'Diario';
			
			$date1 = new DateTime();
			$date1->add(DateInterval::createFromDateString('yesterday'));
			$Date1 = $date1->format('Y-m-d');
			$Date1Nice = $date1->format('d/m/Y');
			
			$Date2 = $Date1;
			$Date2Nice = $Date1Nice;
			
			$date3 = new DateTime($Date1);
			$date3->modify('-1 day');
			$Date3 = $date3->format('Y-m-d');
			$Date3Nice = $date3->format('d/m/Y');
			
			$Date4 = $Date3;
			$Date4Nice = $Date3Nice;
			
			$MonthNice1 = $Date1Nice;
			$MonthNice2 = $Date3Nice;
			
			
			$date5 = new DateTime($date1->format('Y-m-01'));
			$Date5 = $date5->format('Y-m-d');
			//$date5 = new DateTime('first day of this month');
			$PastDays = $date5->diff($date1)->days + 1;
			$DaysOnMonth = $date5->format('t');
			$DaysToFinishMonth = $DaysOnMonth - intval($date1->format('d'));
			
			//echo "DOM: $DaysOnMonth $PastDays";
			//exit();
		}else{
			die('Invalid time range');
		}
	}else{
		die('No time range');
	}
	
	
	echo "Date 1: $Date1 \n Date 2: $Date2 \n Date 3: $Date3 \n Date 4: $Date4 \n ";
	//exit(0);
function checkActiveSales($idAccM){
	/*
	global $db;
	
	$sql = "SELECT COUNT(*) FROM agency  
	WHERE sales_manager_id = $idAccM";
	
	if($db->getOne($sql) > 0){
		return true;
	}else{
		return false;
	}
	*/
	return true;
}



	$NcPM = 0;
	$RowsPM = "";
	$BGColor1 = "#FCFCFC";
	$BGColor2 = "#EEEEEE";
	$Detail = "";
	$Top5 = "";
	
	$ArrayRowsSalesManagers = array();
	$ArrayRowsSalesManagersRevenue = array();
	$RowsSalesManagers = '';
	$sql = "SELECT * FROM user WHERE roles LIKE '%ROLE_SALES_MANAGER%' AND status = 1 AND is_email_notification_enabled = 1";
	$query = $db3->query($sql);

	if($db3->num_rows($query) > 0){

		while($AccM = $db3->fetch_array($query)){
			$idUser = $AccM['id'];
			$Email = $AccM['email'];
			$Name = $AccM['name'] . ' ' . $AccM['last_name'];
			$SalesNames[$idUser] = $Name;
			$AccMNick = $AccM['nick'];
			$CreatedAt = new DateTime($AccM['created_at']);
			
			$DaysOnCompany = $CreatedAt->diff($date1)->days;

			if(checkActiveSales($idUser)){
				
				
			    $FirstDay = $date1->format('Y-m-') . '01';
			    $LastDate = $date1->format('Y-m-d');
			    			    
			    $sql = "SELECT COUNT(DISTINCT(idCampaing)) FROM reports
					INNER JOIN campaign ON campaign.id = reports.idCampaing
					INNER JOIN agency ON campaign.agency_id = agency.id 
					WHERE agency.sales_manager_id = '$idUser' AND campaign.type = 1 AND reports.Date BETWEEN '$Date5' AND '$Date1' AND reports.Impressions > 0";
				$ActiveDeals = $db->getOne($sql);
				
				$sql = "SELECT COUNT(DISTINCT(idCampaing)) FROM reports
					INNER JOIN campaign ON campaign.id = reports.idCampaing
					INNER JOIN agency ON campaign.agency_id = agency.id 
					WHERE agency.sales_manager_id = '$idUser' AND campaign.type = 2 AND reports.Date BETWEEN '$Date5' AND '$Date1' AND reports.Impressions > 0";
				$ActiveCamp = $db->getOne($sql);
			    
			    $sql = "SELECT SUM(Revenue) FROM reports
				INNER JOIN campaign ON campaign.id = reports.idCampaing
				INNER JOIN agency ON campaign.agency_id = agency.id 
				WHERE agency.sales_manager_id = '$idUser' AND reports.Date BETWEEN '$FirstDay' AND '$LastDate'";
			    $CurrentRevenue = $db->getOne($sql);
			    
			    $sql = "SELECT SUM(Revenue) FROM reports
				INNER JOIN campaign ON campaign.id = reports.idCampaing
				INNER JOIN agency ON campaign.agency_id = agency.id 
				WHERE agency.sales_manager_id = '$idUser' AND reports.Date BETWEEN '$Date1' AND '$Date1'";
			    $YesterdayRevenue = $db->getOne($sql);
			    
			    $sql = "SELECT SUM(Revenue) FROM reports
				INNER JOIN campaign ON campaign.id = reports.idCampaing
				INNER JOIN agency ON campaign.agency_id = agency.id 
				WHERE agency.sales_manager_id = '$idUser' AND reports.Date BETWEEN '$Date3' AND '$Date3'";
			    $BeforeYesterdayRevenue = $db->getOne($sql);
			    
			    if($YesterdayRevenue > $BeforeYesterdayRevenue){
				    $Diff = $YesterdayRevenue - $BeforeYesterdayRevenue;
				    $Porce = $Diff / $YesterdayRevenue * 100;
				    
				    $ProcVar = '+' . number_format($Porce, 2, ',', '.') . '%';
				    $ProcVarColor = 'green';
			    }elseif($YesterdayRevenue < $BeforeYesterdayRevenue){
				    $Diff = $BeforeYesterdayRevenue - $YesterdayRevenue;
				    $Porce = $Diff / $YesterdayRevenue * 100;
				    
				    $ProcVar = '-' . number_format($Porce, 2, ',', '.') . '%';
				    $ProcVarColor = 'red';
			    }else{
				    $ProcVar = '0.00%';
				    $ProcVarColor = 'black';
			    }
			    
			    
			    $sql = "SELECT goal FROM target WHERE month = " . $date1->format('m') . " AND year = " . $date1->format('Y') . " AND user_id = $idUser LIMIT 1";
			    $Goal = $db3->getOne($sql);
			    $DayGoal = $Goal / $DaysOnMonth;
			    
			    
			    $RevenueDiario = '';
			    if($Goal > $CurrentRevenue){
				    $GoalPerDay = ($Goal - $CurrentRevenue) / $DaysToFinishMonth;
				    $RevenueDiario = '$' . number_format($YesterdayRevenue, 2, ',', '.') . ' / $' . number_format($GoalPerDay, 2, ',', '.');
				    
				    if($GoalPerDay > $YesterdayRevenue){
					    $SColorPO = 'red';
				    }elseif($GoalPerDay < $YesterdayRevenue){
					    $SColorPO = 'green';
				    }else{
					    $SColorPO = 'black';
				    }
			    }else{
				    $GoalPerDay = 0;
				    $RevenueDiario = '$' . number_format($YesterdayRevenue, 2, ',', '.') . ' / -';
				    $SColorPO = 'black';
			    }
			    
			    /*
			    if($Goal > 0){
			    	if($YesterdayRevenue > $DayGoal){
				    	$Dif = $YesterdayRevenue - $DayGoal;
						
						if($DayGoal > 0){
							$Porc = $Dif / $DayGoal * 100;
						}else{
							$Porc = 100;
						}
						$PorcentajeObjetivo = "+" . number_format($Porc, 2, ',', '.');
						$SColorPO = 'green';
			    	}elseif($YesterdayRevenue < $DayGoal){
				    	$Dif = $DayGoal - $YesterdayRevenue;
				    	
				    	if($YesterdayRevenue > 0){
							$Porc = $Dif / $DayGoal * 100;
						}else{
							$Porc = 100;
						}
						$PorcentajeObjetivo = "-" . number_format($Porc, 2, ',', '.');
						$SColorPO = 'red';
			    	}else{
				    	$PorcentajeObjetivo = '0';
				    	$SColorPO = 'black';
			    	}
			    	$RevenueDiario = '$' . number_format($YesterdayRevenue, 2, ',', '.') . ' / $' . number_format($DayGoal, 2, ',', '.');
			    }else{
				    $RevenueDiario = '$' . number_format($YesterdayRevenue, 2, ',', '.');
				    $SColorPO = 'black';
			    }
			    */
				
				$ShouldBe = $DayGoal * $PastDays;
				
				if($ShouldBe > $CurrentRevenue){
					$SColorGoal = 'red';
				}elseif($ShouldBe < $CurrentRevenue){
					$SColorGoal = 'green';
				}else{
					$SColorGoal = 'black';
				}
				
				if($Goal > $CurrentRevenue){
					//$DiffAc = $Goal - $CurrentRevenue;
					$Porce = 100 * $CurrentRevenue / $Goal;
					$AccomplishPorc = number_format($Porce, 0, ',', '.');
				}else{
					$AccomplishPorc = '100';
				}
				
				$indexCurrentRevenue = round($CurrentRevenue * 100000);
				
				if($indexCurrentRevenue == 0){
					$indexCurrentRevenue = $idUser;
				}
				
				if(!array_key_exists($indexCurrentRevenue, $ArrayRowsSalesManagersRevenue)){
					$ArrayRowsSalesManagersRevenue[$indexCurrentRevenue] = '';
				}

				$ArrayRowsSalesManagersRevenue[$indexCurrentRevenue] .= '<tr style="background-color: #BGC#;">
				    <td style="font-family: sans-serif; color:black;">' . $AccMNick . '</td>
				    <td style="font-family: sans-serif; color:' . $SColorGoal . ';">$' . number_format($CurrentRevenue, 0, ',', '.') . ' / $' . number_format($Goal, 0, ',', '.') . ' (' . $AccomplishPorc . '%)</td>
				    <td style="font-family: sans-serif; color:' . $SColorPO . ';">' . $RevenueDiario . '</td>
				    <td style="font-family: sans-serif; color:' . $ProcVarColor . ';">' . $ProcVar . '</td>
					<td style="font-family: sans-serif; color:black;">' .  $ActiveDeals . '</td>
					<td style="font-family: sans-serif; color:black;">' .  $ActiveCamp . '</td>
				    <td class="hidem" style="font-family: sans-serif; color:black;">' . $DaysOnCompany . '</td>
				</tr>';
				
				$ArrayRowsSalesManagers[$idUser] = '<tr style="background-color: ' . $BGColor1 . ';">
				    <td style="font-family: sans-serif; color:' . $SColorGoal . ';">$' . number_format($CurrentRevenue, 0, ',', '.') . ' / $' . number_format($Goal, 0, ',', '.') . ' (' . $AccomplishPorc . '%)</td>
				    <td style="font-family: sans-serif; color:' . $SColorPO . ';">' . $RevenueDiario . '</td>
				    <td style="font-family: sans-serif; color:' . $ProcVarColor . ';">' . $ProcVar . '</td>
					<td style="font-family: sans-serif; color:black;">' .  $ActiveDeals . '</td>
					<td style="font-family: sans-serif; color:black;">' .  $ActiveCamp . '</td>
				    <td class="hidem" style="font-family: sans-serif; color:black;">' . $DaysOnCompany . '</td>
				</tr>';
				
				//<td style="font-family: sans-serif; color:' . $SColorGoal . ';">$' . number_format($CurrentRevenue, 0, ',', '.') . ' / $' . number_format($Goal, 0, ',', '.') . '</td>
			}
			//sleep(40);
		}
	}
	
	$Nc = 0 ;
	krsort($ArrayRowsSalesManagersRevenue);
	foreach($ArrayRowsSalesManagersRevenue as $TdSales){
		if($Nc % 2 == 0){
		    $BGColor = $BGColor1;
	    }else{
		    $BGColor = $BGColor2;
	    }
		
		$RowsSalesManagers .= str_replace('#BGC#', $BGColor, $TdSales);
		
		$Nc++;
	}
	
	$sql = "SELECT 
			campaign.id AS idCampaign, 
			campaign.name AS Deal, 
			campaign.deal_id AS DealID, 
			ssp.name AS Exchange, 
			CONCAT(user.name, ' ', user.last_name) AS SalesManager,
			user.id AS idUser,
			campaign.volume AS Budget,
			campaign.end_at AS EndDate,
			SUM(reports.Revenue) AS RevA 
			
			FROM campaign
			
			INNER JOIN reports ON campaign.id = reports.idCampaing
			INNER JOIN ssp ON campaign.ssp_id = ssp.id 
			INNER JOIN agency ON campaign.agency_id = agency.id 
			INNER JOIN user ON agency.sales_manager_id = user.id
			WHERE reports.Date = '$Date1' AND reports.Impressions > 0
            GROUP BY DealID";
	
	$RowsActiveDeals = '';
	$ArrayRowsActiveDeals = array();
	
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		$Nc = 0;
		while($Row = $db->fetch_array($query)){
			$idCampaign = $Row['idCampaign'];
			$sql = "SELECT COUNT(*) FROM reports WHERE Impressions > 0 AND Date < '$Date1' AND idCampaing = $idCampaign";
			if($db->getOne($sql) == 0){// || 1==1
			
				$idUser = $Row['idUser'];
				
				if(!array_key_exists($idUser, $ArrayRowsActiveDeals)){
					$ArrayRowsActiveDeals[$idUser] = '';
				}
				
				if($Row['Budget'] > 0){
					$Budget = number_format($Row['Budget'], 2, ',', '.');
				}else{
					$Budget = '-';
				}
				if($Row['EndDate'] != '0000-00-00 00:00:00'){
					$dateC = new DateTime();
					$dateC->add(DateInterval::createFromDateString($Row['EndDate']));
					$EndDate = $date1->format('d-m-Y');
				}else{
					$EndDate = '-';
				}
				
				if($Nc % 2 == 0){
				    $BGColor = $BGColor1;
			    }else{
				    $BGColor = $BGColor2;
			    }
				
				$RowsActiveDeals .= '<tr style="background-color: ' . $BGColor . ';">
				    <td style="font-family: sans-serif; color:black;">' . $Row['SalesManager'] . '</td>
				    <td style="font-family: sans-serif; color:black;">' . $Row['DealID'] . '</td>
				    <td style="font-family: sans-serif; color:black;" class="hidem">' . $Row['Deal'] . '</td>
				    <td style="font-family: sans-serif; color:black;">' . $Row['Exchange'] . '</td>
				    <td style="font-family: sans-serif; color:black;">$' . number_format($Row['RevA'], 2, ',', '.') . '</td>
				    <td style="font-family: sans-serif; color:black;" class="hidem" >' . $Budget . '</td>
					<td class="hidem" style="font-family: sans-serif; color:black;">' . $EndDate . '</td>
				</tr>';
				
				$ArrayRowsActiveDeals[$idUser] .= '<tr style="background-color: ' . $BGColor . ';">
				    <td style="font-family: sans-serif; color:black;">' . $Row['Deal'] . '</td>
				    <td style="font-family: sans-serif; color:black;" class="hidem" >' . $Row['DealID'] . '</td>
				    <td style="font-family: sans-serif; color:black;">' . $Row['Exchange'] . '</td>
				    <td style="font-family: sans-serif; color:black;" class="hidem" >' . $Budget . '</td>
					<td class="hidem" style="font-family: sans-serif; color:black;">' . $EndDate . '</td>
				</tr>';
				
				$Nc++;
			}
		}
	}
	

	//VARIACIONES	
	$sql = "SELECT 
			campaign.id AS idDeal,
			campaign.name AS Deal, 
			campaign.deal_id AS DealID, 
			CONCAT(user.name, ' ', user.last_name) AS SalesManager,
			user.id AS idUser,
			SUM(reports.Revenue) AS Revenue
			
			FROM campaign
			
			INNER JOIN reports ON campaign.id = reports.idCampaing
			INNER JOIN agency ON campaign.agency_id = agency.id 
			INNER JOIN user ON agency.sales_manager_id = user.id
			WHERE reports.Date = '$Date3' AND reports.Impressions > 0
            GROUP BY idDeal";
            
	$RowsADVVariations = '';
	$RowsADVVariationsIn = '';
	$RowsADVVariationsDe = '';
	$ArrayRowsADVVariationsIn = array();
	$ArrayRowsADVVariationsDe = array();
	$ArrayRowsADVVariationsInU = array();
	$ArrayRowsADVVariationsDeU = array();
	
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		$Nc = 0;
		while($Row = $db->fetch_array($query)){
			$idDeal = $Row['idDeal'];
			$idUser = $Row['idUser'];
			
			if(!array_key_exists($idUser, $ArrayRowsADVVariationsDe)){
				$ArrayRowsADVVariationsDe[$idUser] = '';
			}
			if(!array_key_exists($idUser, $ArrayRowsADVVariationsIn)){
				$ArrayRowsADVVariationsIn[$idUser] = '';
			}
			
			$sql = "SELECT SUM(reports.Revenue) AS Revenue FROM reports WHERE idCampaing = $idDeal AND Date = '$Date1'";
			$YesterdayRevenue = $db->getOne($sql);
			
			if($Nc % 2 == 0){
			    $BGColor = $BGColor1;
		    }else{
			    $BGColor = $BGColor2;
		    }
			
			if($YesterdayRevenue > $Row['Revenue']){

				$Dif = $YesterdayRevenue - $Row['Revenue'];						
				if($Row['Revenue'] > 0){
					$Porc = $Dif / $Row['Revenue'] * 100;
				}else{
					$Porc = 100;
				}
						
				if($Porc >= 15 && $Dif >= 4){
					
					//$indexDif = round($Dif * 1000);
					$indexDif = round($YesterdayRevenue * 100000);
					
					$ArrayRowsADVVariationsIn[$indexDif] = '<tr style="background-color: #BGC#;">
					    <td style="font-family: sans-serif; color:green;">' . $Row['SalesManager'] . '</td>
					    <td style="font-family: sans-serif; color:green;">' . $Row['DealID'] . '</td>
					    <td style="font-family: sans-serif; color:green;" class="hidem">' . $Row['Deal'] . '</td>
						<td style="font-family: sans-serif; color:green;">$' . number_format($YesterdayRevenue, 2, '.', ',') . '</td>
						<td style="font-family: sans-serif; color:green;">$' . number_format($Row['Revenue'], 2, '.', ',') . '</td>
						<td style="font-family: sans-serif; color:green;" class="hidem">' . number_format($Porc, 2, '.', ',') . '%</td>
					</tr>';
					
					$ArrayRowsADVVariationsInU[$idUser][$indexDif] = '<tr style="background-color: #BGC#;">
					    <td style="font-family: sans-serif; color:green;" class="hidem">' . $Row['DealID'] . '</td>
					    <td style="font-family: sans-serif; color:green;">' . $Row['Deal'] . '</td>
						<td style="font-family: sans-serif; color:green;">$' . number_format($YesterdayRevenue, 2, '.', ',') . '</td>
						<td style="font-family: sans-serif; color:green;">$' . number_format($Row['Revenue'], 2, '.', ',') . '</td>
						<td style="font-family: sans-serif; color:green;" class="hidem">' . number_format($Porc, 2, '.', ',') . '%</td>
					</tr>';
					
					$Nc++;
					
				}
				
			}elseif($Row['Revenue'] > $YesterdayRevenue){
				
				$Dif = $Row['Revenue'] - $YesterdayRevenue;
				if($YesterdayRevenue > 0){
					$Porc = $Dif / $YesterdayRevenue * 100;
				}else{
					$Porc = 100;
				}
						
				if($Porc >= 15 && $Dif >= 4){
					
					//$indexDif = round($Dif * 1000);
					$indexDif = round($YesterdayRevenue * 100000);
				
					$ArrayRowsADVVariationsDe[$indexDif] = '<tr style="background-color: #BGC#;">
					    <td style="font-family: sans-serif; color:red;">' . $Row['SalesManager'] . '</td>
					    <td style="font-family: sans-serif; color:red;">' . $Row['DealID'] . '</td>
					    <td style="font-family: sans-serif; color:red;" class="hidem">' . $Row['Deal'] . '</td>
						<td style="font-family: sans-serif; color:red;">$' . number_format($YesterdayRevenue, 2, '.', ',') . '</td>
						<td style="font-family: sans-serif; color:red;">$' . number_format($Row['Revenue'], 2, '.', ',') . '</td>
						<td style="font-family: sans-serif; color:red;" class="hidem">-' . number_format($Porc, 2, '.', ',') . '%</td>
					</tr>';
					
					$ArrayRowsADVVariationsDeU[$idUser][$indexDif] = '<tr style="background-color: #BGC#;">
					    <td style="font-family: sans-serif; color:red;">' . $Row['DealID'] . '</td>
					    <td style="font-family: sans-serif; color:red;" class="hidem">' . $Row['Deal'] . '</td>
						<td style="font-family: sans-serif; color:red;">$' . number_format($YesterdayRevenue, 2, '.', ',') . '</td>
						<td style="font-family: sans-serif; color:red;">$' . number_format($Row['Revenue'], 2, '.', ',') . '</td>
						<td style="font-family: sans-serif; color:red;" class="hidem">-' . number_format($Porc, 2, '.', ',') . '%</td>
					</tr>';
					
					$Nc++;
					
				}
				
			}
		}
	}
	
	$Nc = 0;
	krsort($ArrayRowsADVVariationsDe);
	krsort($ArrayRowsADVVariationsIn);
	foreach($ArrayRowsADVVariationsIn as $TdVar){
		if($Nc % 2 == 0){
		    $BGColor = $BGColor1;
	    }else{
		    $BGColor = $BGColor2;
	    }
		
		$RowsADVVariationsIn .= str_replace('#BGC#', $BGColor, $TdVar);
		
		$Nc++;
	}
	foreach($ArrayRowsADVVariationsDe as $TdVar){
		if($Nc % 2 == 0){
		    $BGColor = $BGColor1;
	    }else{
		    $BGColor = $BGColor2;
	    }
		
		$RowsADVVariationsDe .= str_replace('#BGC#', $BGColor, $TdVar);
		
		$Nc++;
	}
	
	
	$RowsADVVariations = $RowsADVVariationsIn . $RowsADVVariationsDe;
	
//	exit(0);
	
	$MailContent = file_get_contents('/var/www/html/login/emailstpl/' . $TPL);
	$MailContent = str_replace('#RowsSM#', $RowsPM, $MailContent);
	
	if($RowsActiveDeals == ''){
		$RowsActiveDeals  = '<tr><td colspan="6" style="font-family: sans-serif; color:black; text-align:center;">No se activo ningún nuevo Deal ayer</td></tr>';
	}
	$MailContent = str_replace('#RowsActiveDeals#', $RowsActiveDeals, $MailContent);
	
	$MailContent = str_replace('#RowsADVVariations#', $RowsADVVariations, $MailContent);
	
	$MailContent = str_replace('#RowsSalesManagers#', $RowsSalesManagers, $MailContent);
	$MailContent = str_replace('#Top5#', $Top5, $MailContent);

	$MailContent = str_replace('#Date1#', $Date1Nice, $MailContent);
	$MailContent = str_replace('#Date2#', $Date2Nice, $MailContent);
	$MailContent = str_replace('#Date3#', $Date3Nice, $MailContent);
	$MailContent = str_replace('#Date4#', $Date4Nice, $MailContent);
	
	$MailContent = str_replace('#NiceMonth1#', $MonthNice1, $MailContent);
	$MailContent = str_replace('#NiceMonth2#', $MonthNice2, $MailContent);
	
	
	$MailContent = str_replace('#HidemS#', $HidemS, $MailContent);
	$MailContent = str_replace('#Hidem#', $Hidem, $MailContent);
	
	//echo $MailContent;
	//exit(0);
	
	$Subject = "Reporte de Anunciantes $RepType ($Date1Nice)";
	//exit(0);
	//MAIL MARCOS
	$mail = new PHPMailer;
				
	$mail->isSMTP();
	$mail->SMTPDebug = 0;
	$mail->Debugoutput = 'html';
	
	$mail->Host = 'smtp.gmail.com';
	$mail->Port = 465;
	$mail->SMTPSecure = 'ssl';
	$mail->SMTPAuth = true;
	$mail->Username = "notifysystem@vidoomy.net";
	$mail->Password = "NoTyFUCK05-1";
	$mail->CharSet = 'UTF-8';
	$mail->setFrom('notifysystem@vidoomy.net', 'Vidoomy');
	$mail->addReplyTo('notifysystem@vidoomy.net', 'Vidoomy');

	
	$UserName = 'Antonio Simarro';
	//$UserEmail = 'marcos.cuesta@vidoomy.com';
	$UserEmail = 'antonio.simarro@vidoomy.com';
	//$UserEmail = 'federico.izuel@vidoomy.com';
	
	$mail->addAddress($UserEmail, $UserName);
	$mail->AddBCC('federico.izuel@vidoomy.com');
	$mail->AddBCC('gadiel.reyesdelrosario@vidoomy.com');
	
	$mail->Subject = $Subject;
	$mail->msgHTML(str_replace('#MarcosEric#','Tony',$MailContent));
	//$mail->send();
	
	//exit(0);
	
	//MAIL ERIC
	$mail2 = new PHPMailer;
				
	$mail2->isSMTP();
	$mail2->SMTPDebug = 0;
	$mail2->Debugoutput = 'html';
	
	$mail2->Host = 'smtp.gmail.com';
	$mail2->Port = 465;
	$mail2->SMTPSecure = 'ssl';
	$mail2->SMTPAuth = true;
	$mail2->Username = "notifysystem@vidoomy.net";
	$mail2->Password = "NoTyFUCK05-1";
	$mail2->CharSet = 'UTF-8';
	$mail2->setFrom('notifysystem@vidoomy.net', 'Vidoomy');
	$mail2->addReplyTo('notifysystem@vidoomy.net', 'Vidoomy');

	
	$UserName = 'Eric Raventos';
	$UserEmail = 'eric.raventos@vidoomy.com';
	
	$mail2->addAddress($UserEmail, $UserName);
	$mail2->Subject = $Subject;
	$mail2->msgHTML(str_replace('#MarcosEric#','Eric',$MailContent));
	//$mail2->send();
	
	//exit(0);
	
	foreach($ArrayRowsSalesManagers as $idUser => $RowSalesManager){
		
		
		$MailContentSales = file_get_contents('/var/www/html/login/emailstpl/' . $TPLSales);
		
		$MailContentSales = str_replace('#RowsSalesManagers#', $RowSalesManager, $MailContentSales);
		
		$RowVariations = '';
		if(array_key_exists($idUser, $ArrayRowsADVVariationsDe)){
			$RowVariations .= $ArrayRowsADVVariationsDe[$idUser];
		}
		if(array_key_exists($idUser, $ArrayRowsADVVariationsIn )){
			$RowVariations .= $ArrayRowsADVVariationsIn[$idUser];
		}
		$MailContentSales = str_replace('#RowsADVVariations#', $RowVariations, $MailContentSales);
		
		
		$RowActiveDeals = '';
		if(array_key_exists($idUser, $ArrayRowsActiveDeals )){
			$RowActiveDeals .= $ArrayRowsActiveDeals[$idUser];
		}
		$MailContentSales = str_replace('#RowsActiveDeals#', $RowActiveDeals, $MailContentSales);
		
		$Subject = "Reporte de Anunciantes $RepType ($Date1Nice)";
		$mail = new PHPMailer;
					
		$mail->isSMTP();
		$mail->SMTPDebug = 0;
		$mail->Debugoutput = 'html';
		
		$mail->Host = 'smtp.gmail.com';
		$mail->Port = 465;
		$mail->SMTPSecure = 'ssl';
		$mail->SMTPAuth = true;
		$mail->Username = "notifysystem@vidoomy.net";
		$mail->Password = "NoTyFUCK05-1";
		$mail->CharSet = 'UTF-8';
		$mail->setFrom('notifysystem@vidoomy.net', 'Vidoomy');
		$mail->addReplyTo('notifysystem@vidoomy.net', 'Vidoomy');
	
		
		$UserName = $SalesNames[$idUser];
		//$UserEmail = 'eric.raventos@vidoomy.com';
		$UserEmail = 'federico.izuel@vidoomy.com';
		$MailContentSales = str_replace('#MarcosEric#', $UserName, $MailContentSales);
		
		$mail->addAddress($UserEmail, $UserName);
		//$mail->AddBCC('federico.izuel@vidoomy.com');
		
		$mail->Subject = $Subject;
		$mail->msgHTML($MailContentSales);
		$mail->send();
		
		//echo $MailContentSales;
		//exit(0);
	}
	