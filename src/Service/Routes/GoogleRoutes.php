<?php

namespace App\Service\Routes;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class GoogleRoutes
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getRoutes(array $addresses, string $serviceProviderFullAddress): array
    {
        try {
            $opts = ['json' => [
                'intermediates' => array_map(static fn(string $address) => ['address' => $address], $addresses),
                'origin' => ['address' => $serviceProviderFullAddress],
                'destination' => ['address' => $serviceProviderFullAddress],
                'travelMode' => 'DRIVE',
                'routingPreference' => 'TRAFFIC_AWARE',
            ]];
            $response = $this->httpClient->request('POST', 'https://routes.googleapis.com/directions/v2:computeRoutes', $opts);
            return $response->toArray();
        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException('Failed to get routes from Google: ' . $e->getMessage());
        }
    }
}