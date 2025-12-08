<?php

namespace App\Service\Routes;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class GoogleRoutes
{
    private HttpClientInterface $httpClient;
    private string $googleApiKey;
    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->googleApiKey = $_ENV['GOOGLE_API_KEY'];
    }

    public function getRoutes(array $addresses, string $serviceProviderFullAddress): array
    {
        $addresses = array_values(array_unique($addresses, SORT_STRING));
        $body = [
            'intermediates' => array_map(static fn(string $address) => ['address' => $address], $addresses),
            'origin' => ['address' => $serviceProviderFullAddress],
            'destination' => ['address' => $serviceProviderFullAddress],
            'travelMode' => 'DRIVE',
            'routingPreference' => 'TRAFFIC_AWARE',
            'optimizeWaypointOrder' => true,
        ];
        try {
            $opts = [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-Goog-Api-Key' => $this->googleApiKey,
                    'X-Goog-FieldMask' => '*',
                ],
                'json' => $body,
            ];
            $response = $this->httpClient->request('POST', 'https://routes.googleapis.com/directions/v2:computeRoutes', $opts);
            $optimalOrderOfAddresses = $response->toArray()['routes'][0]['optimizedIntermediateWaypointIndex'];
            $orderedAddresses = array_map(static fn(int $index) => $addresses[$index], $optimalOrderOfAddresses);
            return $response->toArray();
        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException('Failed to get routes from Google: ' . $e->getMessage());
        }
    }
}
