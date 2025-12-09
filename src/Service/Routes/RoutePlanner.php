<?php

namespace App\Service\Routes;

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

    public function getRouteDeliveries(ServiceProvider $serviceProvider, \DateTimeImmutable $date): array
    {
        return $this->deliveriesFetcher->getDeliveriesForDate($serviceProvider, $date);
    }
    
    public function getRouteAddresses(array $deliveries): array
    {
        return array_map(static fn(Order $order): string => $order->getClient()->getAddress(), $deliveries);
    }

    public function planRouteForDate(ServiceProvider $serviceProvider, \DateTimeImmutable $date): array
    {
        $deliveries = $this->getRouteDeliveries($serviceProvider, $date);
        $addresses = $this->getRouteAddresses($deliveries);
        $uniqueAddresses = array_values(array_unique($addresses, SORT_STRING));
        if (empty($uniqueAddresses)) {
            return [];
        }
        function splitAddressBlock(array &$addressBlock, array &$parentArray, int $indexInParentArray): void
        {
            $blockLength = \count($addressBlock);
            $halfBlock = \array_slice($addressBlock, 0, $blockLength / 2);
            $otherHalf = \array_slice($addressBlock, $blockLength / 2);
            $parentArray[$indexInParentArray] = $halfBlock;
            $parentArray[] = $otherHalf;
        }
        function getTimeSpentAtAddressesInBlock(array $addressBlock, array $originalAddresses): int
        {
            $totalTimeForBlock = 0;
            foreach ($addressBlock as $addressInBlock) {
                $totalTimeForBlock += 5 * 60; // 5 minutes per address
                foreach ($originalAddresses as $addressInOriginalArray) {
                    if ($addressInBlock === $addressInOriginalArray) {
                        $totalTimeForBlock += 2 * 60; // 2 minutes extra per extra order at same address
                    }
                }
            }
            return $totalTimeForBlock;
        }
        function combineCarTripsUntilTimeLimit(array $carTrips): array {
            $combinedCarTrips = [];
            foreach ($carTrips as $carTrip) {
                if (!empty($combinedCarTrips) && end($combinedCarTrips)['routeDurationInSeconds'] + $carTrip['routeDurationInSeconds'] <= 60 * 60 * 6) {
                    $combinedCarTrips[array_key_last($combinedCarTrips)]['orderedAddresses'] = [...end($combinedCarTrips)['orderedAddresses'], ...$carTrip['orderedAddresses']];
                    $combinedCarTrips[array_key_last($combinedCarTrips)]['routeDurationInSeconds'] += $carTrip['routeDurationInSeconds'];
                } else {
                    $combinedCarTrips[] = $carTrip;
                }
            }
            return $combinedCarTrips;
        }
        $carTrips = [];
        $routeCalculationDone = false;
        $serviceProviderFullAddress = $serviceProvider->getFullAddress();
        $addressBlocks = [$uniqueAddresses];
        while (!$routeCalculationDone && !empty($addressBlocks)) {
            $carTrips = [];
            $index = 0;
            foreach ($addressBlocks as $addressBlock) {
                if (\count($addressBlock) > 25) {
                    splitAddressBlock($addressBlock, $addressBlocks, $index);
                    break;
                }
                $routeData = $this->googleRoutes->getRoutes($addressBlock, $serviceProviderFullAddress);

                $timeSpentAtAddresses = getTimeSpentAtAddressesInBlock($addressBlock, $addresses);
                $totalTime = $routeData['routeDurationInSeconds'] + $timeSpentAtAddresses;
                $totalTimeExceedsLimit = $totalTime > 60 * 60 * 6;
                if ($totalTimeExceedsLimit && \count($addressBlock) !== 1) { // 6 hours
                    splitAddressBlock($addressBlock, $addressBlocks, $index);
                    break;
                } else {
                    $carTrips[] = $routeData;
                }
                $index++;
            }
            if (\count($addressBlocks) === \count($carTrips)) {
                $routeCalculationDone = true;
            }
        }
        return ['deliveries' => $deliveries, 'carTrips' => combineCarTripsUntilTimeLimit($carTrips)];
    }
}