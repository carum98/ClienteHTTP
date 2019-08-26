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

    public function publishProduct($sellerId, $productData)
    {
        return $this->makeResquest('POST',"sellers/{$sellerId}/products", [], $productData,[],true);
    }

    public function setProductCategory($productId, $categoryId)
    {
        return $this->makeResquest('PUT',"products/{$productId}/categories/{$categoryId}");
    }

    public function updateProduct($sellerId, $productId, $productData)
    {
        $productData['_method'] = 'PUT';
        return $this->makeResquest('POST',"sellers/{$sellerId}/products/{$productId}", [], $productData,[], isset($productData['picture']));
    }

    public function purchaseProduct($productId, $buyerId, $quantity)
    {
        return $this->makeResquest('POST',"products/{$productId}/buyers/{$buyerId}/transactions", [], ['quantity' => $quantity]);
    }

    public function getCategories()
    {
        return $this->makeResquest('GET','categories');
    }

    public function getCategoryProduct($id)
    {
        return $this->makeResquest('GET',"categories/{$id}/products");
    }

    public function getUserInformation()
    {
        return $this->makeResquest('GET','users/me');
    }

    public function getPurchases($buyerId)
    {
        return $this->makeResquest('GET',"buyers/{$buyerId}/products");
    }

    public function getPublications($sellerId)
    {
        return $this->makeResquest('GET',"sellers/{$sellerId}/products");
    }
}