<?php

namespace App;
use QuickBooksOnline\API\Core\OAuth\OAuth2;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Exception\SdkException;


use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class QbToken extends Model
{
    /**
    * @return DataService
    */

    public function GetDataService()
    {
        try {
            $qbConfig = config('qbConfig');
            $ClientID = $qbConfig['ClientID'];
            $ClientSecret = $qbConfig['ClientSecret'];

            $QbTokens = $this->where('id', '>', -1)->get();
            if(count($QbTokens) > 0){
                $accessTokenKey = $QbTokens[0]->accesstoken;
                $refreshTokenKey = $QbTokens[0]->refreshtoken;
                $QBORealmID = $QbTokens[0]->realmid;
                
                // Prep Data Services
                $dataService = DataService::Configure(array(
                     'auth_mode' => 'oauth2',
                     'ClientID' => $ClientID,
                     'ClientSecret' => $ClientSecret,
                     'accessTokenKey' => 'OAuth 2 Access Token',
                     'refreshTokenKey' => $refreshTokenKey,
                     'QBORealmID' => $QBORealmID,
                     'baseUrl' => env('QB_BASE_URL'),
                ));
                try {
                    $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
                    $refreshedAccessTokenObj = $OAuth2LoginHelper->refreshToken();
                    $error = $OAuth2LoginHelper->getLastError();
                    if(!$error && !is_null($refreshedAccessTokenObj)){
                      //Refresh Token is called successfully
                        $dataService->updateOAuth2Token($refreshedAccessTokenObj);
                        $accesstoken = $refreshedAccessTokenObj->getAccessToken();
                        $refreshtoken = $refreshedAccessTokenObj->getRefreshToken();
                        $this->where('id', $QbTokens[0]->id)->update(
                            [
                                'accesstoken' => $accesstoken,
                                'refreshtoken' => $refreshtoken,
                            ]
                        );
                        return ['dataService' => $dataService, 'refreshtoken' => $refreshtoken];
                    }
                } catch (\ServiceException $e) {
                    return ['dataService' => null];            
                }
            }

            $dataService = DataService::Configure($qbConfig);
            $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();

            // Get the Authorization URL from the SDK
            $authUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();
            return ['dataService' => null, 'authUrl' => $authUrl];
        } catch (SdkException $e) {
            return ['dataService' => null];            
        }
    }

    public function RevokeToken()
    {
    	# code...
    	$result = $this->GetDataService();
    	$dataService = $result['dataService'];

    	if(!is_null($dataService)){
            $QbTokens = $this->where('id', '>', -1)->get();
            if(count($QbTokens) > 0){
                $QbToken = $QbTokens[0];
        		$refreshtoken = $result['refreshtoken'];
    	        $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
    	        try {
    				$revokeResult = $OAuth2LoginHelper->revokeToken($refreshtoken);
    				if($revokeResult){
    					DB::table('qb_tokens')->truncate();
    				}
    	        } catch (ServiceException $e) {
    				return redirect('/');
    	        }
            }
	   	}
    }
}
