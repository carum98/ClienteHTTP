<?php

namespace App\Traits;

use GuzzleHttp\Client;

trait ConsumesExternalServices
{
    /**
     * Enviar Request a servicios
     * @return string
     */
    public function makeResquest($method, $requestUrl, $queryParams = [], $formParams = [], $headers = [])
    {
        $client = new Client([
            'base_uri' => $this->baseUri,
        ]);

        if (method_exists($this, 'resolveAutorization')) {
            $this->resolveAutorization($queryParams,$formParams,$headers);
        }

        $response = $client->request($method, $requestUrl, [
            'query' => $queryParams,
            'form_params' => $formParams,
            'headers' => $headers
        ]);

        $response = $response->getBody()->getContents();

        if (method_exists($this, 'decodeResponse')) {
            $response = $this->decodeResponse($response);
        }

        if (method_exists($this, 'checkIfErrorResponse')) {
            $this->checkIfErrorResponse($response);
        }

        return $response;
    }
}