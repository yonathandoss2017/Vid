<?php

// Endpoint that will provide information from campaigns created in VMP and are currently active.

// FUNCTIONS 

if(! function_exists('build_date')) {
    function build_date($date) {
        try {
            return new DateTime($date);
        } catch (\Throwable $th) {
            header('HTTP/1.0 400 Bad Request');
            echo sprintf('"Invalid date: %s"', $date);
            exit(0);
        }
    }
}

if(! function_exists('get_adv_investments')) {
    /**
     * Get advertisers investment
     *
     * @return array
     */
    function get_adv_investments($type, $campaignQuery, $countriesISO = []) {
        $investments = [];
        $investmentRanges= [
          // key               date from                          date to       
            'yesterday'   => [(new DateTime())->modify("-1 day"), new DateTime()],
            'last_month'  => [
                (new DateTime())->modify("-1 month")->modify("first day of this month"), 
                (new DateTime())->modify("-1 month")->modify("last day of this month")
            ],
            'last_7_days' => [(new DateTime())->modify("-7 day"), new DateTime()],
            'this_month'  => [(new DateTime())->modify("first day of this month"), new DateTime()],
            'this_year'   => [(new DateTime())->modify("first day of january"), new DateTime()],
        ];

        foreach($investmentRanges as $period => $range) {
            $investmentsTotals = get_total_investments($campaignQuery, $countriesISO, $range[0], $range[1]);
            $investment = $investmentsTotals['investment'];
            $impressions = $investmentsTotals['impressions'];
            $response = compact('investment', 'period');
            
            if($type === 'impressions') {
                $response = compact('impressions', 'period');
            }

            $investments[] = $response;
        }

        return $investments;
    }
}

if(! function_exists('get_total_investments')) {
    /**
     * Gets the total investments data
     *
     * @return array
     */
    function get_total_investments($campaignQuery, $countriesISO = [], $startDate = null, $endDate = null) {
        $where = [];
        $sql = "SELECT 
            SUM(investment) AS investment,
            SUM(impressions) AS impressions
        FROM 
        (
            SELECT  
                SUM(revenue) AS investment,
                SUM(impressions) AS impressions
            FROM 
                reports AS r,
                country AS c
            WHERE c.id = r.idCountry
            AND_WHERE
            GROUP BY c.id, c.iso
            ORDER BY investment ASC
        ) AS total_investment";

        $where[] = sprintf(" r.idCampaing IN (%s) " , $campaignQuery);

        if($countriesISO) {
            $where[] = sprintf(" c.iso IN ('%s') " , join("','",$countriesISO));
        }

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

        $db = get_database_connection();
        $totalInvestments = $db->getAll($sql)[0];

        $totalInvestments['investment'] = (float) $totalInvestments['investment'] ?? 0;
        $totalInvestments['impressions'] = (float) $totalInvestments['impressions'] ?? 0;

        return $totalInvestments;
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

if(! function_exists('get_campaigns_sql')) {
    /**
     * Get the Query to retrieve the active campaigns from VMP
     *
     * @return array
     */
    function get_campaigns_sql($companyId = null, $advertisersId = []) {
        $sql = "SELECT id 
            FROM campaign 
            WHERE status = 1
            AND from_vmp IS NULL";

        $where = [];

        if($companyId) {
            $where[] ="agency_id = " . $companyId;
        }

        if($advertisersId) {
            $where[] .= sprintf (" advertiser_id IN (%s) ", join(',',$advertisersId));
        }

        if($where) {
            $sql .= sprintf(" AND %s ", join(' AND', $where));
        }

        return $sql;
    }
}

if(! function_exists('get_adv_investment_by_country')) {
    /**
     * Get advertisers investment by country
     *
     * @return array
     */
    function get_adv_investment_by_country($type, $campaignQuery, $countriesISO = [], $startDate = null, $endDate = null) {
        $totalInvestments = get_total_investments($campaignQuery, $countriesISO, $startDate, $endDate);
        $totalInvestment = $totalInvestments[$type];
        $sql = "SELECT  
                    SUM(revenue) AS investment,
                    SUM(impressions) AS impressions,
                    c.iso AS country
                FROM 
                    reports AS r,
                    country AS c";
        $where = [
            'c.id = r.idCountry',
            sprintf(" r.idCampaing IN (%s) " , $campaignQuery)
        ];

        if($countriesISO) {
            $where[] = sprintf(" c.iso IN ('%s') " , join("','",$countriesISO));
        }

        if($startDate) {
            $where[] = sprintf(" r.date >= '%s'", $startDate->format('Y-m-d'));
        }

        if($endDate) {
            $where[] = sprintf(" r.date <= '%s'", $endDate->format('Y-m-d'));
        }
        
        if($where) {
            $sql .= sprintf(" WHERE %s ", join(' AND', $where));
        }

        $sql .= 'GROUP BY c.id, c.iso';

        $db = get_database_connection();
        $results = $db->getAll($sql);

        $investments = [];

        foreach($results as $result) {
            $result[$type] = (float) $result[$type];
            $investment = (float) $result[$type];
            
            if($totalInvestment <= 0) {
                $totalInvestment = 1;
            }

            if($type === 'impressions') {
                unset($result['investment']);
            }else{
                unset($result['impressions']);
            }

            $percentage = (float) number_format(( $investment * 100) / $totalInvestment, 2);
            $investments[] = array_merge(compact('percentage'), $result);
        }


        return $investments;
    }
}

if(! function_exists('get_adv_last_investment')) {
    /**
     * Get the latest advertisers investment
     *
     * @return void
     */
    function get_adv_last_investment($campaignQuery, $countriesISO = [], $startDate = null, $endDate = null) {
        $investments = [];
        $maxIntervalDays = 7;
        
        if($startDate === null && $endDate === null) {
            $startDate = (new DateTime())->modify(sprintf('-%s days', $maxIntervalDays - 1)); 
            $endDate = new DateTime();
        }

        $interval = (int) $startDate->diff($endDate)->format('%a');

        if($interval >= $maxIntervalDays) {

            return $investments;
        }

        $investmentRanges = [];

        for($i = 1; $i <= $interval; $i++){
            $fromIntervalDate = clone $startDate;
            $toIntervalDate = clone $startDate;
            $toIntervalDate->modify('+1 day');
            $period = sprintf('%s to %s', $fromIntervalDate->format('Y-m-d'), $toIntervalDate->format('Y-m-d'));
            $investmentRanges[$period] = [$fromIntervalDate, $toIntervalDate];
            $startDate->modify("+1 day");
        }

        foreach($investmentRanges as $period => $range) {
            $totalInvestment = get_total_investments($campaignQuery, $countriesISO, $range[0], $range[1]);
            $investment = $totalInvestment['investment'];
            $impressions = $totalInvestment['impressions'];
            $investments[] = compact('investment', 'impressions', 'period');
        }

        return $investments;
    }
}


// API RESPONSE:

@session_start();
ini_set('display_errors', 1);
ini_set('memory_limit', '-1');
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
define('CONST',1);

require('/var/www/html/login/admin/lkqdimport/common.php');
require('/var/www/html/login/config.php');
require('/var/www/html/login/db.php');

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: POST");
header("Allow: POST");
header('Content-Type: application/json');

$params = json_decode(file_get_contents('php://input'), true) ?? $_POST;

if (
    !isset($params['uuid']) ||
    !isset($params['env'])
) {
    header('HTTP/1.0 403 Forbidden');
    echo '"Access denied"';
    exit(0);
}

// $requiredParams = ['company_id', 'countries', 'start_date', 'end_date', 'advertiser_id', 'type'];
$requiredParams = [];

foreach($requiredParams as $key) {
    if(!isset($params[$key])) {
        header('HTTP/1.0 400 Bad Request');
        echo sprintf('"Missing required parameters: %s"', implode(', ', $requiredParams));
        exit(0);
    }
}

$companyId = $params['company_id'] ?? null;
$countriesISO = $params['countries'] ?? [];
$startDate = isset($params['start_date']) ? build_date($params['start_date']) : null;
$endDate = isset($params['end_date']) ? build_date($params['end_date']) : null;
$advertisersId = $params['advertiser_id'] ?? null;
$type = $params['type'] ?? 'investment';

if($type !== 'impressions' && $type !== 'investment') {
    header('HTTP/1.0 400 Bad Request');
    echo sprintf('"Invalid type value. Allowed values: investment, impressions"');
    exit(0);
}

if($startDate !== null && $endDate !== null) {
    if($endDate < $startDate) {
        header('HTTP/1.0 400 Bad Request');
        echo sprintf('"Invalid dates range"');
        exit(0);
    }
}

$campaignQuery = get_campaigns_sql($companyId, $advertisersId);

$investment = [];
$investmentByCountry = [];
$lastInvestment = [];

$investment = get_adv_investments($type, $campaignQuery, $countriesISO);
$investmentByCountry = get_adv_investment_by_country($type, $campaignQuery, $countriesISO, $startDate, $endDate);
$lastInvestment = get_adv_last_investment($campaignQuery, $countriesISO, $startDate, $endDate);

$data = [
    'investment' => $investment,
    'investmentByCountry' => $investmentByCountry,
    'lastInvestment' => $lastInvestment
];

header('HTTP/1.0 200 Success');
echo json_encode($data); // response

