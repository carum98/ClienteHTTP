<?php

namespace App\Services;

use App\Traits\ConsumesExternalServices;
use App\Traits\AuthorizesMarketRequest;
use App\Traits\InteractWithMarcketReponses;

class MarketService
{
    use ConsumesExternalServices, AuthorizesMarketRequest, InteractWithMarcketReponses;
    
    protected $baseUri;
    
    public function __construct()
    {
        $this->baseUri = config('services.market.base_uri');
    }

}