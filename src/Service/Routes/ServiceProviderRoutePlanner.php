<?php

namespace App\Service\Routes;

class ServiceProviderRoutePlanner
{
    public function __construct(
        private GoogleRoutes $googleRoutes,
    ) {
    }

    public function planRouteFromAddresses(array $addresses): array
    {
        return $this->googleRoutes->getRoutes($addresses);
    }
}