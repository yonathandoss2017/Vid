<?php	
	@session_start();
	define('CONST',1);

    if (file_exists('/var/www/html/login/config.php')) {
        require('/var/www/html/login/config.php');
    } else {
        require('../../config_local.php');
    }

    require('../../db.php');

    $VALID_PARAMS = [
        'uuid',
        'env',
        'campaign_id',
        'start_date',
        'end_date'
    ];

    // Validate unvalid params
    foreach ($_POST as $key => $value) {
        $invalidParams = [];
        if (!in_array($key, $VALID_PARAMS)) {
            $invalidParams[] = $key;
        }
    }

    if (count($invalidParams) > 0) {
        header('HTTP/1.0 400 Bad Request');
        echo sprintf("Invalid param: %s", join($invalidParams));
		exit(0);
    }

    if(!isset($_POST['uuid']) || !isset($_POST['env'])){
		header('HTTP/1.0 403 Forbidden');
		echo 'Access denied';
		exit(0);
	}

    if (!isset($_POST['campaign_id']) ||
        !is_numeric($_POST['campaign_id']) ||
        intval($_POST['campaign_id']) != $_POST['campaign_id']
    ) {
        header('HTTP/1.0 400 Bad Request');
		echo 'Missing or invalid campaign_id';
		exit(0);
    }

if(! function_exists('get_campaign_sql')) {
    function get_campaign_sql($campaign_id) {
        $sql = "SELECT *
            FROM campaign
            WHERE id = $campaign_id
            AND status = 1
            AND from_vmp = 1";

        return $sql;
    }
}

if(! function_exists('get_database_connection')) {
    /**
     * Return the DB connection
     */
    function get_database_connection() {
        global $dbhost;
        global $dbAdvName;
        global $dbuser;
        global $dbpass;
        
        $connection = new SQL($dbhost, $dbAdvName, $dbuser, $dbpass);

        return $connection;
    }
}

if(! function_exists('build_date')) {
    function build_date($date) {
        if (!$date) return null;

        $date = str_replace(array('"', '\''), "", $date);

        try {
            return new DateTime($date);
        } catch (\Throwable $th) {
            header('HTTP/1.0 400 Bad Request');
            echo sprintf('"Invalid date: %s"', $date);
            exit(0);
        }
    }
}

if(! function_exists('get_campaign_stats')) {
    /**
     * Gets campaign stats
     *
     * @return array
     */
    function get_campaign_stats($campaignQuery, $startDate = null, $endDate = null) {
        $where = [];
        $sql = "SELECT  
                SUM(revenue) AS investment,
                SUM(impressions) AS impressions,
                SUM(clicks) AS clicks,
                SUM(CompleteV) AS complete_v,
                SUM(VImpressions) as v_impressions
            FROM 
                reports AS r";

        $where[] = sprintf(" r.idCampaing IN (%s) " , $campaignQuery);

        if($startDate) {
            $where[] = sprintf(" r.date >= '%s'", $startDate->format('Y-m-d'));
        }

        if($endDate) {
            $where[] = sprintf(" r.date <= '%s'", $endDate->format('Y-m-d'));
        }

        $andWhere = '';

        if($where) {
            $andWhere .= sprintf("AND %s ", join(' AND', $where));
        }

        $sql = str_replace('AND_WHERE', $andWhere, $sql);

        global $db;
        $campaignStats = $db->getAll($sql)[0];

        $campaignStats['investment'] = (float) $campaignStats['investment'] ?? 0;
        $campaignStats['impressions'] = (float) $campaignStats['impressions'] ?? 0;
        $campaignStats['clicks'] = (float) $campaignStats['clicks'] ?? 0;
        $campaignStats['Complete_v'] = (float) $campaignStats['Complete_v'] ?? 0;
        $campaignStats['v_impressions'] = (float) $campaignStats['v_impressions'] ?? 0;

        return $campaignStats;
    }
}

$db = get_database_connection();
$campaignQuery = get_campaign_sql($_POST['campaign_id']);
$campaign = $db->getAll($campaignQuery)[0];
$startDate = build_date($_POST['start_date']);
$endDate = build_date($_POST['end_date']);

if (!$campaign) {
    header('HTTP/1.0 404 Not Found');
    echo "Not Found";
    exit(0);
}

$campaignStats = get_campaign_stats($campaignQuery, $startDate, $endDate);

$ctr = $campaignStats['clicks'] / $campaignStats['impressions'];
$cpv = $campaignStats['investment'] / $campaignStats['complete_v'];
$vtr = $campaignStats['complete_v'] / $campaignStats['impressions'] * 100;
$viewability = $campaignStats['v_impressions'] / $campaignStats['impressions'] * 100;

$campaignData = [
    "total_delivery" => [
        "impressions" => $campaignStats['impressions']
    ],
    "ctr" => $ctr,
    "vtr" => $vtr,
    "viewability" => $viewability,
    "delivery_metrics" => [
        "clicks" => $campaignStats['clicks'],
        "cpv" => $cpv
    ]
];

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: POST");
header('Content-Type: application/json; charset=utf-8');
header("Allow: POST");

echo json_encode($campaignData);
?>

