<?php

namespace App\Service\Routes;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GoogleRoutes
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getRoutes(array $addresses): array
    {
        $response = $this->httpClient->request('POST', 'https://routes.googleapis.com/directions/v2:computeRoutes', [
            'json' => [
                'addresses' => $addresses,
            ],
        ]);
        return $response->toArray();
    }
}