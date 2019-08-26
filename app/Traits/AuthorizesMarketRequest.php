<?php

namespace App\Traits;

use App\Services\MarketAuthenticationService;

trait AuthorizesMarketRequest
{
    public function resolveAutorization(&$queryParams,&$formParams,&$headers)
    {
        $accessToken = $this->resolveAccessToken();
        $headers['Authorization'] = $accessToken;
    }

    public function resolveAccessToken()
    {
        $authenticationService = resolve(MarketAuthenticationService::class);

        if (auth()->user()) {
            return $authenticationService->getAutenticationUserToken();
        }
        return $authenticationService->getClientCredentialsToken();
    }

}