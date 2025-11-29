<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use App\Enum\MealTypes;

final class OrderCreationDto
{
    public function __construct(
        #[Assert\NotBlank]
        public int $orderSize,
        #[Assert\Type(\DateTimeInterface::class)]
        public \DateTimeInterface $date,
        #[Assert\NotBlank]
        public int $clientId,
        #[Assert\NotBlank]
        public int $serviceProviderId,
        #[Assert\NotBlank]
        #[Assert\Choice(callback: [MealTypes::class, 'values'])]
        public string $mealType,
    ) {
    }
}

