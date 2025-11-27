<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class ClientDto
{
    /**
     * @param string[] $schedule
     * @param int[] $serviceProviderIds
     */
    public function __construct(
        #[Assert\NotBlank]
        public string $name,
        #[Assert\Count(min: 1)]
        #[Assert\All([
            new Assert\NotBlank(),
            new Assert\Type('string'),
        ])]
        public array $schedule,
        #[Assert\NotBlank]
        public string $address,
        #[Assert\NotBlank]
        public string $city,
        public string $country,
        #[Assert\All([
            new Assert\Type('integer'),
        ])]
        public array $serviceProviderIds = [],
    ) {
    }
}

