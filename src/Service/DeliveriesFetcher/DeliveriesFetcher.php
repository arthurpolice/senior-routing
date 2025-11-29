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
        $deliveries = $this->serviceProviderRepository->findDeliveriesWithClientsForDate($serviceProvider, $date);
        $todaysDeliveries = array_filter($deliveries, static function(Order $order) use ($date): bool {
            $orderWeekDay = WeekDays::from(strtolower($date->format('l')));
            $isClientAvailable = \in_array($orderWeekDay->value, $order->getClient()->getSchedule(), true);
            return $isClientAvailable;
        });
        return $todaysDeliveries;
    }
}