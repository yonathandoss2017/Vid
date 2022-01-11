<?php

namespace App\Report\Http\Controller;

use App\Report\Exception\UnauthorizedException;
use App\Report\Http\Response;
use App\Report\Manager\Advertisers\AgencyManager;
use App\Report\Manager\Advertisers\CampaignManager;
use App\Report\Manager\Advertisers\InvestmentManager;
use App\Report\Manager\Advertisers\UserManager;
use App\Report\Security\JWTAuth;
use Throwable;
use UnexpectedValueException;

class CampaignVMPInvestmentController extends Controller {

    /**
     * Execute the GET request
     *
     * @return Response
     */
    public function get() {
        try {
            $this->checkJWTAuthorization('VMP');
            $params = $this->getRequestPayload();

            $this->validatePayload([
                'company_id'    => ['required','integer','min:1'],
                'type'          => 'required|in:impressions,investment',
                'advertiser_id' => ['array', function (&$values) use (&$params) {
                    if(!$values) {
                        return false;
                    }
                    
                    $atLeastOneInt = false;
        
                    foreach($values as $index => $value) {
                        if(is_int($value) && $value > 0) {
                            $atLeastOneInt = true;
                        }else{
                            unset($values[$index]);
                        }
                    }
        
                    $params['advertiser_id'] = $values;
        
                    if(!$atLeastOneInt) {
                        return ":attribute must contain at least one positive integer.";
                    }
                }],
                'iso_code'     => ['array', function ($values) {
                    if(! is_array($values)) {
                        return false;
                    };
        
                    foreach($values as $value) {
                        if(strlen($value) !== 2) {
                            return ":attribute contains a invalid ISO code ($value). Code length should be: 2";
                        }
                    }
                }],
            ]);
        
            $companyId = $params['company_id'] ?? null;
            $countriesISO = $params['iso_code'] ?? [];
            $startDate = null;
            $endDate = null;
            $advertisersId = $params['advertiser_id'] ?? [];
            $type = $params['type'];
        
            $this->checkAgency($companyId);
            $campaignQuery = CampaignManager::getByCampaignAndAdvertisersSQL($companyId, $advertisersId);
            $investment = InvestmentManager::getInvestments($type, $campaignQuery, $countriesISO);
            $investmentByCountry = InvestmentManager::getInvestmentByCountry($type, $campaignQuery, $countriesISO, $startDate, $endDate);
            $lastInvestment = InvestmentManager::getLastInvestment($campaignQuery, $countriesISO, $startDate, $endDate);
        
            $content = compact('investment', 'investmentByCountry', 'lastInvestment');
            
            return Response::json($content);
        } catch (UnauthorizedException $uae) {
            
            return Response::unauthorized();
        } catch (UnexpectedValueException $sie) {
            
            return Response::badRequest($sie->getMessage());
        } catch (Throwable $th) {
            error_log($th->getMessage());
            return Response::internalServerError();
        }
    }

    /**
     * @param int $agencyId
     * @return void
     * @throws UnexpectedValueException
     */
    public function checkAgency(int $agencyId) {
        $agency = AgencyManager::getById($agencyId);

        if(!$agency) {
            throw new UnexpectedValueException('"Company with id: ' . $agencyId . ' not found"');
        }
    }
}
