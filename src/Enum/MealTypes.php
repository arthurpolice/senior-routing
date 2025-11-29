<?php

namespace App\Enum;

enum MealTypes: string 
{
    case LUNCH = 'lunch';
    case DINNER = 'dinner';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }
}