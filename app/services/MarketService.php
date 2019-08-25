<?php

namespace App\Services;

use App\Traits\AuthorizesMarketRequest;
use App\Traits\ConsumesExternalServices;
use App\Traits\InteractWithMarcketReponses;

class MarketService
{
    use ConsumesExternalServices, AuthorizesMarketRequest, InteractWithMarcketReponses;
    
    protected $baseUri;
    
    public function __construct()
    {
        $this->baseUri = config('services.market.base_uri');
    }

    public function getProducts()
    {
        return $this->makeResquest('GET','products');
    }

    public function getProduct($id)
    {
        return $this->makeResquest('GET',"products/{$id}");
    }

    public function getCategories()
    {
        return $this->makeResquest('GET','categories');
    }

    public function getCategoryProduct($id)
    {
        return $this->makeResquest('GET',"categories/{$id}/products");
    }
}