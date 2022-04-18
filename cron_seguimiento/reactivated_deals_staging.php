<?php

@session_start();
// Guardamos cualquier error //
ini_set('display_errors', 0);
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
define('CONST', 1);
define('AAL_DEAL_ID', '1053459');

if (file_exists('/var/www/html/login/config.php')) {
    require('/var/www/html/login/config.php');
    require('/var/www/html/login/constantes.php');
    require('/var/www/html/login/db.php');
    require('/var/www/html/login/common.lib.php');
    require '/var/www/html/site/include/PHPMailer/PHPMailerAutoload.php';
} else {
    require('./../config_local.php');
    require('./../constantes.php');
    require('./../db.php');
    require('./../common.lib.php');
    require './../include/PHPMailer/PHPMailerAutoload.php';
}

$db = new SQL($dbhost, 'vidoomy_adv', $dbuser, $dbpass);
$dbPanel = new SQL($advDev02['host'], $advDev02['db'], $advDev02['user'], $advDev02['pass']);

$date1 = new DateTime();
$date1->add(DateInterval::createFromDateString('today'));
$Today = $date1->format('Y-m-d');

$date2 = new DateTime();
$date2->add(DateInterval::createFromDateString('yesterday'));
$Yesterday = $date2->format('Y-m-d');

$date2->modify('-2 days');
$ThreeDaysBefore = $date2->format('Y-m-d');
// $Today = '2020-05-04';

// echo $Yesterday . ' - ' . $ThreeDaysBefore;

/**
 * Function to set the campaign first impression date
 */
function setFistImpressionDate(int $idCampaing)
{
    global $dbPanel;

    $date = (new DateTime())
            ->setTimeZone(new DateTimeZone('US/Eastern'))
            ->format('Y-m-d H:m:s');

    $sql = <<<SQL
UPDATE
    campaign
SET
    first_impression = '{$date}'
WHERE
    id = {$idCampaing}
SQL;

    $dbPanel->query($sql);
}

function getUserManagersEmails($userId, $addOwnEmail = false)
{
    $user = getUser($userId);

    $emails = [];

    if (!$user) {
        return $emails;
    }

    if ($addOwnEmail) {
        $Roles = json_decode($user['roles']);
        if (!in_array('ROLE_ADMIN', $Roles)) {
            $emails = [$user['email']];
        }
    }

    if (
        !isset($user['manager_id'])
        || !$user['manager_id']
        || (int) $user['manager_id'] === (int) $userId
    ) {
        return $emails;
    }

    return array_merge($emails, getUserManagersEmails($user['manager_id'], true));
}

function getUser($userId)
{
    global $advProd;
    $db3 = new SQL($advProd["host"], $advProd["db"], $advProd["user"], $advProd["pass"]);
    $sql = "SELECT * FROM user WHERE id = $userId";

    return $db3->getFirst($sql);
}

function sendActivationNotice($Type, $idCampaing, $Today)
{
    global $db, $advProd;
    if ($idCampaing != 3521 && $idCampaing != 3707 && $idCampaing != 3552 && $idCampaing != 5417) {
        $db3 = new SQL($advProd["host"], $advProd["db"], $advProd["user"], $advProd["pass"]);

        $sql = "SELECT user.email AS Email FROM account_manager_campaigns AS amc INNER JOIN user ON user.id = amc.user_id WHERE campaign_id = {$idCampaing}";
        $queryManagers = $db3->query($sql);

        $managers = array();
        if ($db3->num_rows($queryManagers) > 0) {
            while ($Manager = $db3->fetch_array($queryManagers)) {
                $managers[] = $Manager['Email'];
            }
        }

        $sql = "SELECT * FROM campaign WHERE id = $idCampaing LIMIT 1";
        $queryCamp = $db3->query($sql);
        $CampData = $db3->fetch_array($queryCamp);

        $purchaseOrderId = $CampData['purchase_order_id'];

        $sql = "SELECT CONCAT(user.name, ' ', user.last_name) AS Name, user.email AS Email, user.manager_id AS HeadID FROM purchase_order INNER JOIN user ON user.id = purchase_order.sales_manager_id WHERE purchase_order.id = $purchaseOrderId";

        $querySales = $db3->query($sql);
        $SalesData = $db3->fetch_array($querySales);

        $NameSalesManager = $SalesData['Name'];
        $EmailSalesManager = $SalesData['Email'];
        $HeadId = intval($SalesData['HeadID']);

        $DealId = $CampData['deal_id'];

        if (strpos($DealId, '(') !== false && strpos($DealId, ')') !== false) {
            $dEx = explode('(', $DealId);
            $DealId = $dEx[0];
        }

        $DealName = $CampData['name'];

        $OriginalTpl = file_get_contents('/var/www/html/login/emailstpl/new_deal.html');
        $EmailText = str_replace('#SalesManager#', $NameSalesManager, $OriginalTpl);
        if ($CampData['type'] == 2) {
            $EmailText = str_replace('#DealCamp#', 'la Campaña', $EmailText);
            $Art = 'La campaña';
        } else {
            $EmailText = str_replace('#DealCamp#', 'el Deal', $EmailText);
            $Art = 'El deal';
        }
        $EmailText = str_replace('#CampName#', $DealName, $EmailText);
        $EmailText = str_replace('#DealID#', $DealId, $EmailText);

        if ($Type == 1) {
            $EmailTitle = "$Art $DealName ha reactivado";

            $EmailText = str_replace('#Primera_70H#', 'Ha vuelto a comprar luego de más de 3 días :)', $EmailText);
            $EmailText = str_replace('#RE#', 're', $EmailText);
        } else {
            $EmailTitle = "$Art $DealName ha empezado a comprar";

            $EmailText = str_replace('#Primera_70H#', 'Hemos detectado la primera impresión :D', $EmailText);
            $EmailText = str_replace('#RE#', '', $EmailText);
        }

        if ($DealName != '' && strlen($DealId) > 2) {
            $sql = "INSERT INTO sent_activation (idCampaing, Date, Type) VALUES ($idCampaing, '$Today', '$Type')";
            $db->query($sql);

            $mail = new PHPMailer();
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
            $mail->setFrom('notify@vidoomy.net', 'Vidoomy');
            $mail->addReplyTo('notify@vidoomy.net', 'Vidoomy');

            //$EmailSalesManager = 'federicoizuel@gmail.com';
            $mail->addAddress($EmailSalesManager, $NameSalesManager);

            $mail->AddBCC('federico.izuel@vidoomy.com');
            $mail->AddBCC('gadiel.reyesdelrosario@vidoomy.com');
            $mail->AddBCC('marcos.cuesta@vidoomy.com');
            $mail->AddBCC('eric.raventos@vidoomy.com');
            $mail->AddBCC('finance@vidoomy.com');
            $mail->AddBCC('ernesto.gonzalez@vidoomy.com');
            $mail->AddBCC('francisco.murcia@vidoomy.com');
            $mail->AddBCC('mayte.santos@vidoomy.com');
            $mail->AddBCC('patricia.palmero@vidoomy.com');
            $mail->AddBCC('nicolle.garcia@vidoomy.com');

            foreach ($managers as $managerEmail) {
                $mail->AddBCC($managerEmail);
            }

            if (intval($HeadId) > 0) {
                $headManagersEmail = getUserManagersEmails($HeadId, true);
                if ($headManagersEmail) {
                    foreach ($headManagersEmail as $headManagerEmail) {
                        $mail->AddCC($headManagerEmail);
                    }
                }
            }

            $mail->Subject = $EmailTitle;// . " (Fe de erratas)"
            $mail->msgHTML($EmailText);
            $mail->send();

            $mail = new PHPMailer();
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
            $mail->setFrom('notify@vidoomy.net', 'Vidoomy');
            $mail->addReplyTo('notify@vidoomy.net', 'Vidoomy');

            $mail->addAddress('antonio.simarro@vidoomy.com', 'Antonio Simarro');

            if (AAL_DEAL_ID == $DealId) {
                $mail->AddBCC('ernesto.gonzalez@vidoomy.com', 'Ernesto Gonzalez');
            }

            $mail->Subject = $EmailTitle;
            $mail->msgHTML(str_replace($NameSalesManager, 'Tony', $EmailText));
            $mail->send();
        }
    }
}


$sql = "SELECT DISTINCT(idCampaing) AS idCampaing FROM reports 
INNER JOIN campaign ON campaign.id = reports.idCampaing
WHERE reports.Date = '$Today' AND reports.Impressions > 0";

$query = $db->query($sql);

if ($db->num_rows($query) <= 0) {
    return;
}

while ($TodayDeals = $db->fetch_array($query)) {
    $idCampaing = $TodayDeals['idCampaing'];

    $sql = "SELECT COUNT(*) FROM sent_activation WHERE idCampaing = $idCampaing AND Type = 0";

    if (intval($db->getOne($sql)) == 0) {
        $sql = "SELECT COUNT(*) FROM reports WHERE Date < '$Today' AND Impressions > 0 AND idCampaing = $idCampaing";
        if ($db->getOne($sql) == 0) {
            sendActivationNotice(0, $idCampaing, $Today);
            setFistImpressionDate($idCampaing);
            //echo "sendActivationNotice(0, $idCampaing) \n";
        }
    }

    $sql = "SELECT COUNT(*) FROM sent_activation WHERE idCampaing = $idCampaing AND Date = '$Today'";
    if (intval($db->getOne($sql)) == 0) {
        $sql = "SELECT COUNT(*) FROM reports WHERE Date BETWEEN '$ThreeDaysBefore' AND '$Yesterday' AND Impressions > 0 AND idCampaing = $idCampaing";
        if ($db->getOne($sql) == 0) {
            sendActivationNotice(1, $idCampaing, $Today);
            //echo "sendActivationNotice(1, $idCampaing) \n";
        }
    }
}
