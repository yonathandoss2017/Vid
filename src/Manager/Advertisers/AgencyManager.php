<?php

namespace App\Report\Manager\Advertisers;

class AgencyManager extends BaseManager
{
    public static function getById($agencyId) {
        $agencyId = static::sanitize($agencyId);

        $sql = "SELECT * FROM agency WHERE id = $agencyId";
        return static::getConnection()->getFirst($sql);
    }
}