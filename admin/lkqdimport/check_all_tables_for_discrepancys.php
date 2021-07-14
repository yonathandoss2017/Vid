<?php	
	//exit();
	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 1);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/common.lib.php');
	require '/var/www/html/site/include/PHPMailer/PHPMailerAutoload.php';
	require('/var/www/html/login/admin/lkqdimport/common.php');

	$MaxDif = 0;

function calculateDifference($MainValue, $CompValue){
	global $MaxDif;
	
	if($MainValue != $CompValue){
		if($MainValue > $CompValue){
			$Dif = $MainValue - $CompValue;
			$Porc = $Dif / $MainValue * 100;
		}else{
			$Dif = $CompValue - $MainValue;
			$Porc = $Dif / $MainValue * 100;
		}
	}else{
		$Dif = 0;
	}
	
	if($Dif > $MaxDif){
		$MaxDif = $Dif;
	}
	
	if($Dif >= 0.1){
		return '<span style="color:red;">$' . number_format($Dif, 4, '.', ',') . ' (%' . number_format($Porc, 4, '.', ',') . ')</span>';
	}else{
		return '<span style="color:green;">$' . number_format($Dif, 4, '.', ',') . ' (%' . number_format($Porc, 4, '.', ',') . ')</span>';
	}
	
}
	
	$Date = new DateTime('yesterday');
	$CheckDate = $Date->format('Y-m-d');

	$TablaName = getTableName($CheckDate);
	$TablaNameResume = getTableNameResume($CheckDate);
	$TablaNameResume2 = str_replace('_', '', $TablaNameResume);
	
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	$dbuser2 = "root";
	$dbpass2 = "Jz8eDbamcNx3TskWzrjzH7g";
	$dbhost2 = "vidoomy-production.cpijmqdfbof9.eu-west-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	//Platilla del correo
	$MailContent = file_get_contents('/var/www/html/login/emailstpl/check_tables.html');
	
	//Tabla Princial Server
	$sql = "SELECT SUM(Coste) FROM `$TablaName` WHERE `Date` = '$CheckDate'";
	$ReportsCoste = $db->getOne($sql);
	$MailContent = str_replace('#CosteSP#', number_format($ReportsCoste, 2, '.', ','), $MailContent);
	$sql = "SELECT SUM(Impressions) FROM `$TablaName` WHERE `Date` = '$CheckDate'";
	$ReportsImp = $db->getOne($sql);
	$MailContent = str_replace('#ImpresionesSP#', $ReportsImp, $MailContent);
	
	//Tabla Resumen Server
	$sql = "SELECT SUM(Coste) FROM `$TablaNameResume` WHERE `Date` = '$CheckDate'";
	$ReportsResumeCoste = $db->getOne($sql);
	$MailContent = str_replace('#CosteR#', number_format($ReportsResumeCoste, 2, '.', ','), $MailContent);
	$sql = "SELECT SUM(Impressions) FROM `$TablaNameResume` WHERE `Date` = '$CheckDate'";
	$ReportsResumeImp = $db->getOne($sql);
	$MailContent = str_replace('#ImpresionesR#', $ReportsResumeImp, $MailContent);
	$MailContent = str_replace('#DifR#', calculateDifference($ReportsCoste, $ReportsResumeCoste), $MailContent);

	//Tabla Stats Server
	$sql = "SELECT SUM(Coste) FROM `stats` WHERE `Date` = '$CheckDate'";
	$StatsSCoste = $db->getOne($sql);
	$MailContent = str_replace('#CosteS#', number_format($StatsSCoste, 2, '.', ','), $MailContent);
	$sql = "SELECT SUM(Impressions) FROM `stats` WHERE `Date` = '$CheckDate'";
	$StatsSImp = $db->getOne($sql);
	$MailContent = str_replace('#ImpresionesS#', $StatsSImp, $MailContent);
	$MailContent = str_replace('#DifS#', calculateDifference($ReportsCoste, $StatsSCoste), $MailContent);
	
	//Tabla Resumen Panel
	$sql = "SELECT SUM(coste) FROM `$TablaNameResume2` WHERE `date` = '$CheckDate'";
	$ReportsResumeCoste = $db2->getOne($sql);
	$MailContent = str_replace('#CosteRP#', number_format($ReportsResumeCoste, 2, '.', ','), $MailContent);
	$sql = "SELECT SUM(impressions) FROM `$TablaNameResume2` WHERE `date` = '$CheckDate'";
	$ReportsResumeImp = $db2->getOne($sql);
	$MailContent = str_replace('#ImpresionesRP#', $ReportsResumeImp, $MailContent);
	$MailContent = str_replace('#DifRP#', calculateDifference($ReportsCoste, $ReportsResumeCoste), $MailContent);
	
	//Tabla Publishers
	$sql = "SELECT SUM(usd_cost) FROM `stats` WHERE `date` = '$CheckDate'";
	$PublishersCoste = $db2->getOne($sql);
	$MailContent = str_replace('#CosteP#', number_format($PublishersCoste, 2, '.', ','), $MailContent);
	$sql = "SELECT SUM(impressions) FROM `stats` WHERE `date` = '$CheckDate'";
	$PublishersImp = $db2->getOne($sql);
	$MailContent = str_replace('#ImpresionesP#', $PublishersImp, $MailContent);
	$MailContent = str_replace('#DifP#', calculateDifference($ReportsCoste, $PublishersCoste), $MailContent);
	
	//Tabla Publishers Paises
	$sql = "SELECT SUM(usd_cost) FROM `stats_country2020` WHERE `date` = '$CheckDate'";
	$PublishersPCoste = $db2->getOne($sql);
	$MailContent = str_replace('#CostePP#', number_format($PublishersPCoste, 2, '.', ','), $MailContent);
	$MailContent = str_replace('#DifPP#', calculateDifference($ReportsCoste, $PublishersPCoste), $MailContent);
	
	
	if($MaxDif > 1){
		
		$Subject = 'Discrepancias Estadísticas Publishers ' . $Date->format('d-m-Y') . ' - ALGO VA MAL';
		$MSG = "Alguna de las tablas parece no coincidir con las demás. Por favor, avisar a Fede o Gadiel para revisión URGENTE.";
		
	}else{
		
		$Subject = 'Discrepancias Estadísticas Publishers ' . $Date->format('d-m-Y') . ' - Todo va bien';
		$MSG = "Esta es una comparación de todos las tablas de estadísticas el día de ayer. No hay discrepancias superiores a $1, todo va bien.";
		
	}
	
	$MailContent = str_replace('#MSG#', $MSG, $MailContent);
	
	//echo $MailContent;
	//exit(0);
	
	
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
	$mail->addAddress('federico.izuel@vidoomy.com', 'Fede');
	
	$mail->Subject = $Subject;
	$mail->msgHTML(str_replace('#Name#', 'Fede', $MailContent));
	$mail->send();
	
	//exit(0);
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
	$mail2->addAddress('gadiel.reyesdelrosario@vidoomy.com', 'Gadiel');
	
	$mail2->Subject = $Subject;
	$mail2->msgHTML(str_replace('#Name#', 'Gadiel', $MailContent));
	$mail2->send();
	
	
	$mail3 = new PHPMailer;
								
	$mail3->isSMTP();
	$mail3->SMTPDebug = 0;
	$mail3->Debugoutput = 'html';
	
	$mail3->Host = 'smtp.gmail.com';
	$mail3->Port = 465;
	$mail3->SMTPSecure = 'ssl';
	$mail3->SMTPAuth = true;
	$mail3->Username = "notifysystem@vidoomy.net";
	$mail3->Password = "NoTyFUCK05-1";
	$mail3->CharSet = 'UTF-8';
	$mail3->setFrom('notifysystem@vidoomy.net', 'Vidoomy');
	$mail3->addAddress('eric.raventos@vidoomy.com', 'Eric');
	
	$mail3->Subject = $Subject;
	$mail3->msgHTML(str_replace('#Name#', 'Eric', $MailContent));
	$mail3->send();