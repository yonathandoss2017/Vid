<?php

namespace App\Report\Manager\Advertisers;

class CampaignManager extends BaseManager
{
    public static function getByCampaignAndAdvertisersSQL($companyId = null, $advertisersId = []) {
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
