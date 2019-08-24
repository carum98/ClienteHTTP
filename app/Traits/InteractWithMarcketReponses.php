<?php

namespace App\Traits;

trait InteractWithMarcketReponses
{
    public function decodeResponse($response)
    {
        $decodeResponse = json_decode($response);
        return $decodeResponse->data ?? $decodeResponse;
    }

    public function checkIfErrorResponse($response)
    {
        if (isset($response->error)) {
            throw new \Exception("Algo fallo: {$response->error}"); 
        }
    }
}