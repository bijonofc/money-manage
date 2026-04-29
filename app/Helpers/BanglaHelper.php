<?php

namespace App\Helpers;

class BanglaHelper
{
    public static function enToBn($number)
    {
        $en = ['0','1','2','3','4','5','6','7','8','9','.'];
        $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯','.'];

        return str_replace($en, $bn, $number);
    }

    public static function currency($amount,$symble='৳')
    {
        // Two decimal & comma formatting
        $formatted = number_format($amount, 2);

        // Convert to Bangla numbers
        return $symble.' '.self::enToBn($formatted) ;
    }
}
