<?php

namespace App\Services;

use App\Traits\ConsumesExternalServices;
use App\Traits\InteractWithMarcketReponses;

class MarketAuthenticationService
{
    use ConsumesExternalServices, InteractWithMarcketReponses;
    
    protected $baseUri;
    protected $clientId;
    protected $clientSecret;
    protected $passwordClienteId;
    protected $passwordClienteSecret;
    
    public function __construct()
    {
        $this->baseUri = config('services.market.base_uri');
        $this->clientId = config('services.market.client_id'); 
        $this->clientSecret = config('services.market.client_secret');
        $this->passwordClienteId = config('services.market.password_client_id');
        $this->passwordClienteSecret = config('services.market.password_client_secret');
    }

    public function getClientCredentialsToken()
    {
        if ($token = $this->existinValidClientCredentialsToken()) {
            return $token;
        }
        $formParams = [
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ];
        
        $tokenData = $this->makeResquest('POST', 'oauth/token', [], $formParams);

        $this->storeValidToken($tokenData, 'client_credentials');
        
        return $tokenData->access_token;
    }

    public function resolveAutrizationUrl()
    {
        $query = http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => route('authorization'),
            'response_type' => 'code',
            'scope' => 'purchase-product manage-products manage-account read-general',
        ]);

        return "{$this->baseUri}/oauth/authorize/?{$query}";
    }

    public function getCodeToken($code)
    {
        $formParams = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => route('authorization'),
            'code' => $code,
        ];
        
        $tokenData = $this->makeResquest('POST', 'oauth/token', [], $formParams);

        $this->storeValidToken($tokenData, 'autorization_code');
        
        return $tokenData;
    }

    public function storeValidToken($tokenData, $grantType)
    {
        $tokenData->token_expired_at = now()->addSecond($tokenData->expires_in - 5);
        $tokenData->access_token = "{$tokenData->token_type} {$tokenData->access_token}";
        $tokenData->grant_type = $grantType;

        session()->put(['current_token' => $tokenData]);
    }

    public function existinValidClientCredentialsToken()
    {
        if (session()->has('current_token')) {
            $tokenData = session()->get('current_token');

            if (now()->lt($tokenData->token_expired_at)) {
                return $tokenData->access_token;
            }
        }
        return false;
    }
}