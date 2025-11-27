<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class RouteRequestDto
{
    public function __construct(
        public int $serviceProviderId,
        #[Assert\Type(\DateTimeInterface::class)]
        public ?\DateTimeInterface $date,
    ) {
    }
}