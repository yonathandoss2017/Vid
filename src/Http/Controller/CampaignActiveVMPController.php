<?php

namespace App\Report\Http\Controller;

use App\Report\Exception\UnauthorizedException;
use App\Report\Http\Response;
use App\Report\Manager\Advertisers\AgencyManager;
use App\Report\Manager\Advertisers\CampaignManager;
use App\Report\Manager\Advertisers\InvestmentManager;
use App\Report\Manager\Advertisers\UserManager;
use App\Report\Security\JWTAuth;
use DateTime;
use Throwable;
use UnexpectedValueException;

class CampaignActiveVMPController extends Controller {

    /**
     * Execute the GET request
     *
     * @return Response
     */
    public function get() {
        try {
            $this->checkJWTAuthorization('VMP');
            $params = $this->getRequestPayload();
            $campaignId = $params['campaign_id'];
            $campaign = $this->checkCampaign($campaignId);
            $stats = $this->getStats($campaign);

            return Response::json($stats);
        } catch (UnauthorizedException $uae) {
            
            return Response::unauthorized();
        } catch (UnexpectedValueException $sie) {
            
            return Response::badRequest($sie->getMessage());
        } catch (Throwable $th) {
            error_log($th->getMessage());
            exit($th->getMessage());
            return Response::internalServerError();
        }
    }

    private function checkCampaign(int $campaignId) {
        $campaign = CampaignManager::getById($campaignId);

        if(!$campaign) {
            throw new UnexpectedValueException('"Campaign with id: ' . $campaignId . ' not found"');
        }

        return $campaign;
    }

    private function getStats($campaign) : array{
        $id = $campaign['id'];
        $impressions = $campaign['impressions'];

        if(!$impressions || $impressions <= 0) {
            $impressions = 1;
        }

        $stats = CampaignManager::getSummaryStatsByCampaignId($id);
        $statsImpressions = $stats['impressions'];

        switch ($campaign['type']) {
            case 2: // CPV
                $statsImpressions = $stats['clicks'];
                break;
            case 3: // CPC
                $statsImpressions = $stats['complete_v'];
                break;
        }

        $bestDelivery = CampaignManager::getBestDeliveryByCampaignId($id);

        return [
            "total_delivery" => [
                "impressions" => $statsImpressions,
                "percentage" => $statsImpressions / $impressions
            ],
            "best_delivery" => $bestDelivery
        ];
    }
}
