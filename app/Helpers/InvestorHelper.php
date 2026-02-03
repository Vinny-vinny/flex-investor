<?php

namespace App\Helpers;

use App\Models\Invoice;
use Illuminate\Support\Str;

class InvestorHelper
{

    public static function genReferenceNumber(): string
    {
        $prefix = 'INV52'; // 5 chars
        $randomLength = 12 - strlen($prefix); // 7 chars

        $random = strtoupper(
            Str::replaceFirst(
                'O', 'K',
                Str::replaceFirst('I', 'L', Str::random($randomLength))
            )
        );

        $ref = $prefix . $random;

        if (Invoice::where('invoice_number', $ref)->exists()) {
            return self::genReferenceNumber();
        }

        return $ref;
    }
}
