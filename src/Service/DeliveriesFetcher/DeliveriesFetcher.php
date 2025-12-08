<?php

namespace App\Service\DeliveriesFetcher;

use App\Entity\ServiceProvider;
use App\Repository\ServiceProviderRepository;
use App\Entity\Order;
use App\Enum\WeekDays;

class DeliveriesFetcher
{
    public function __construct(
        private ServiceProviderRepository $serviceProviderRepository,
    ) {
    }

    public function getDeliveriesForDate(ServiceProvider $serviceProvider, \DateTimeImmutable $date): array
    {
        $windowDays = 2;
        $serviceProviderWithDeliveries = $this->serviceProviderRepository->findDeliveriesWithClientsForDate($serviceProvider, $date, $windowDays)[0];
        $deliveries = $serviceProviderWithDeliveries->getOrders()->toArray();
        $todaysDeliveries = array_filter($deliveries, static function(Order $delivery) use ($date): bool {
            $deliveryWeekDay = WeekDays::from(strtolower($date->format('l')));
            $clientSchedule = $delivery->getClient()->getSchedule();
            $isClientAvailable = \in_array($deliveryWeekDay, $clientSchedule, true);
            return $isClientAvailable;
        });
        return $todaysDeliveries;
    }
}