<?php

namespace App\Report\Http\Controller;

use App\Report\Exception\UnauthorizedException;
use App\Report\Http\Response;
use App\Report\Manager\Advertisers\AgencyManager;
use App\Report\Manager\Advertisers\CampaignManager;
use App\Report\Manager\Advertisers\InvestmentManager;
use App\Report\Manager\Advertisers\UserManager;
use App\Report\Security\JWTAuth;
use Rakit\Validation\Validator;

class CampaignVMPInvestmentController extends Controller {
    public function get() {
        try {
            $this->checkAuthorization();
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
        
            $agency = AgencyManager::getById($companyId);
            if(!$agency) {
                return Response::notFound('"Company with id: ' . $companyId . ' not found"');
            }
        
            $campaignQuery = CampaignManager::getBy($companyId, $advertisersId);
        
            $investment = InvestmentManager::getInvestments($type, $campaignQuery, $countriesISO);
            $investmentByCountry = InvestmentManager::getInvestmentByCountry($type, $campaignQuery, $countriesISO, $startDate, $endDate);
            $lastInvestment = InvestmentManager::getLastInvestment($campaignQuery, $countriesISO, $startDate, $endDate);
        
            $content = json_encode(compact('investment', 'investmentByCountry', 'lastInvestment'));
            return Response::json($content);
        } catch (UnauthorizedException $uae) {
            return Response::unauthorized();
        } catch (\UnexpectedValueException $sie) {
            return Response::badRequest($sie->getMessage());
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            return Response::internalServerError($th->getMessage());
        }
    }

    /**
     * @return void
     * @throws UnauthorizedException|UnexpectedValueException
     */
    private function checkAuthorization() {
        $jwtAuth = new JWTAuth($_ENV['JWT_VMP_SECRET']);
        $user = $jwtAuth->decodeCurrentRequest();
    
        if(!$user || !$user->user_id) {
            throw new UnauthorizedException();
        }

        $user = UserManager::getById($user->user_id);

        if(!$user) {
            throw new UnauthorizedException();
        }
    
        $userIsActive = UserManager::isUserExternalClient($user['id'], 'VMP');
    
        if(!$userIsActive) {
            throw new UnauthorizedException();
        }
    }
}