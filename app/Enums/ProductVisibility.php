<?php

namespace App\Enums;

enum ProductVisibility: string
{
    case VISIBLE = 'visible';
    case HIDDEN = 'hidden';
    case SEARCH_ONLY = 'search_only';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}