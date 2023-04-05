<?php


namespace App\Enums;


use BenSampo\Enum\Enum;

class SearchTypeEnum extends Enum
{
    public const SIMPLE = 'simple';
    public const WITH_INGREDIENTS = 'with_ingredients';
    public const DEEP = 'deep';
}
