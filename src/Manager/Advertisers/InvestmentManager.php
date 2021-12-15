<?php

namespace App\Report\Manager\Advertisers;

use DateTime;

class InvestmentManager extends BaseManager
{
    public static function getInvestments(
        string $type, 
        string $campaignQuery, 
        array $countriesISO = []) 
    {
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
            $investmentsTotals = static::getTotalInvestments($campaignQuery, $countriesISO, $range[0], $range[1]);
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

    public static function getTotalInvestments(
        string $campaignQuery, 
        array $countriesISO = [], 
        DateTime $startDate = null, 
        DateTime $endDate = null) 
    {
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
            $countriesISO = static::sanitize(join(',',$countriesISO));
            $where[] = sprintf(" c.iso IN ('%s') " , $countriesISO);
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

        $totalInvestments = static::getConnection()->getAll($sql)[0];

        $totalInvestments['investment']  = (float) $totalInvestments['investment'] ?? 0;
        $totalInvestments['impressions'] = (float) $totalInvestments['impressions'] ?? 0;
        $totalInvestments['ecpm']        = 0;

        if($totalInvestments['impressions'] > 0 ) {
            $totalInvestments['ecpm'] = $totalInvestments['investment'] / $totalInvestments['impressions'];
        }

        return $totalInvestments;
    }

    public static function getInvestmentByCountry(
        string $type, 
        string $campaignQuery, 
        array $countriesISO = [], 
        DateTime $startDate = null, 
        DateTime $endDate = null) 
    {
        $totalInvestments = static::getTotalInvestments($campaignQuery, $countriesISO, $startDate, $endDate);
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
            $countriesISO = static::sanitize(join(',',$countriesISO));
            $where[] = sprintf(" c.iso IN ('%s') " , $countriesISO);
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

        $results = static::getConnection()->getAll($sql);

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

    public static function getLastInvestment(
        string $campaignQuery, 
        array $countriesISO = [], 
        DateTime $startDate = null, 
        DateTime $endDate = null) 
    {
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
            $totalInvestment = static::getTotalInvestments($campaignQuery, $countriesISO, $range[0], $range[1]);
            $investment = $totalInvestment['investment'];
            $impressions = $totalInvestment['impressions'];
            $ecpm = $totalInvestment['ecpm'];
            $date = $range[0]->format('Y-m-d');
            $investments[] = compact('date', 'investment', 'impressions', 'ecpm');
        }

        return $investments;
    }
}
