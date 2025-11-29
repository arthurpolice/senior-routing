<?php

namespace App\Service\RoutePlanner;

use App\Entity\ServiceProvider;
use App\Service\DeliveriesFetcher\DeliveriesFetcher;
use App\Entity\Order;
use App\Service\Routes\GoogleRoutes;

class RoutePlanner
{
    public function __construct(
        private DeliveriesFetcher $deliveriesFetcher,
        private GoogleRoutes $googleRoutes,
    ) {
    }

    public function getRouteAddresses(ServiceProvider $serviceProvider, \DateTimeImmutable $date): array
    {
        $todaysDeliveries = $this->deliveriesFetcher->getDeliveriesForDate($serviceProvider, $date);
        return array_map(static fn(Order $order): string => $order->getClient()->getAddress(), $todaysDeliveries);
    }

    public function planRouteForDate(ServiceProvider $serviceProvider, \DateTimeImmutable $date): array
    {
        $addresses = $this->getRouteAddresses($serviceProvider, $date);
        if (empty($addresses)) {
            return [];
        }
        if (\count($addresses) > 25) {
            throw new \InvalidArgumentException('Too many addresses to plan route for. Maximum 25 addresses allowed.');
        }
        $serviceProviderFullAddress = $serviceProvider->getFullAddress();
        return $this->googleRoutes->getRoutes($addresses, $serviceProviderFullAddress);
    }
}