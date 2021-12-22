<?php

namespace App\Report\Manager\Advertisers;

use DateTime;

class CampaignManager extends BaseManager
{
    private static function getByIdSQL(int $id, int $status = 1, int $fromVMP = 1) {
        $id = static::sanitize($id);
        $status = static::sanitize($status);
        $fromVMP = static::sanitize($fromVMP);
        
        return "SELECT *
            FROM campaign
            WHERE id = $id
            AND status = $status
            AND from_vmp = $fromVMP";
    }
    
    public static function getById(int $id, int $status = 1, int $fromVMP = 1) {
        $sql = static::getByIdSQL($id, $status, $fromVMP);

        return static::getConnection()->getFirst($sql);
    }

    public static function getBestDeliveryByCampaignId(
        int $id, 
        DateTime $startDate = null, 
        DateTime $endDate = null
    ) {
        $sql = "SELECT 
                impressions,
                clicks,
                date
            FROM 
                reports AS r
            WHERE r.idCampaing = $id 
            AND_WHERE 
            ORDER BY impressions DESC";

        $where = [];

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

        $stats = static::getConnection()->getFirst($sql);
        
        $stats['clicks'] = (float) $stats['clicks'] ?? 0;
        $stats['impressions'] = (float) $stats['impressions'] ?? 0;
        $stats['date'] = $stats['date'];

        return $stats;
    }

    public static function getSummaryStatsByCampaignId(int $id, DateTime $startDate = null, DateTime $endDate = null) {
        $sql = "SELECT  
                SUM(revenue) AS investment,
                SUM(impressions) AS impressions,
                SUM(clicks) AS clicks,
                SUM(CompleteV) AS complete_v,
                SUM(VImpressions) as v_impressions
            FROM 
                reports AS r
            WHERE r.idCampaing = $id 
            AND_WHERE";
        
        $where = [];

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

        $campaignStats = static::getConnection()->getFirst($sql);

        $campaignStats['investment'] = (float) $campaignStats['investment'] ?? 0;
        $campaignStats['impressions'] = (float) $campaignStats['impressions'] ?? 0;
        $campaignStats['clicks'] = (float) $campaignStats['clicks'] ?? 0;
        $campaignStats['complete_v'] = (float) $campaignStats['complete_v'] ?? 0;
        $campaignStats['v_impressions'] = (float) $campaignStats['v_impressions'] ?? 0;

        return $campaignStats;
    }
    
    public static function getByCampaignAndAdvertisersSQL(int $companyId = null, array $advertisersId = []) {
        $sql = "SELECT id 
            FROM campaign 
            WHERE status = 1
            AND from_vmp = 1";

        $where = [];

        if($companyId) {
            $companyId = static::sanitize($companyId);
            $where[] ="agency_id = " . $companyId;
        }

        if($advertisersId) {
            $advertisersId = static::sanitize(join(',',$advertisersId));
            $where[] .= sprintf (" advertiser_id IN (%s) ", $advertisersId);
        }

        if($where) {
            $sql .= sprintf(" AND %s ", join(' AND', $where));
        }

        return $sql;
    }
}
